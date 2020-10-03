<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Stock;
 
class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stock::all();
        
        return view('stocks.index', compact('stocks')); // -> resources/views/stocks/index.blade.php 
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stocks.create'); // -> resources/views/stocks/create.blade.php
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation for required fields (and using some regex to validate our numeric value)
        $request->validate([
            'stock_name'=>'required',
            'ticket'=>'required',
            'value'=>'required|max:10|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/'
        ]); 
        // Getting values from the blade template form
        $stock = new Stock([
            'stock_name' => $request->get('stock_name'),
            'ticket' => $request->get('ticket'),
            'value' => $request->get('value')
        ]);
        $stock->save();
        return redirect('/stocks')->with('success', 'Stock saved.');   // -> resources/views/stocks/index.blade.php
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $jsonurl = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=".$id."&apikey=" . env('AVANTAGE_API_KEY');
        $jsonResponse = file_get_contents($jsonurl);
        $Stack_View   = json_decode($jsonResponse,true);

        $jsonurl = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=".$id."&apikey=".env('AVANTAGE_API_KEY');
        $jsonResponse = file_get_contents($jsonurl);
        $Stack_Global = json_decode($jsonResponse,true);

    
        return view('stocks.show', compact('Stack_View', 'Stack_Global'));
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock = Stock::find($id);
        return view('stocks.edit', compact('stock'));  // -> resources/views/stocks/edit.blade.php
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation for required fields (and using some regex to validate our numeric value)
        $request->validate([
            'stock_name'=>'required',
            'ticket'=>'required',
            'value'=>'required|max:10|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/'
        ]); 
        $stock = Stock::find($id);
        // Getting values from the blade template form
        $stock->stock_name =  $request->get('stock_name');
        $stock->ticket = $request->get('ticket');
        $stock->value = $request->get('value');
        $stock->save();
 
        return redirect('/stocks')->with('success', 'Stock updated.'); // -> resources/views/stocks/index.blade.php
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stock = Stock::find($id);
        $stock->delete(); // Easy right?
 
        return redirect('/stocks')->with('success', 'Stock removed.');  // -> resources/views/stocks/index.blade.php
    } 

    /**
     * Return the Stock current quote from Alpha Vantage.
     * Ideally this function can be split between the Model and a helper Class keeping the Controller small.
     *
     * @param  string  $ticket
     * @return float or false
     */
    public function getQuote($ticket = "AMZN") 
    {
        $jsonurl = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=" . urlencode($ticket) . "&apikey=" . env('AVANTAGE_API_KEY');
        $jsonResponse = file_get_contents($jsonurl);
        $jsonResponseDecode = json_decode($jsonResponse);
        if (isset($jsonResponseDecode->{env('AVANTAGE_API_PARENT')}->{env('AVANTAGE_API_CHILD')})) {  //Validation for empty response(Stock not found). 
            $currentQuote = round($jsonResponseDecode->{env('AVANTAGE_API_PARENT')}->{env('AVANTAGE_API_CHILD')},2);
        } else { 
            $currentQuote = false;
        }
        return $currentQuote;
    }
    /**
     * Update Stock value to most current quote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuote($id) 
    {
        $stock = Stock::find($id);
        $updatedQuote = self::getQuote($stock->ticket);
        if ($updatedQuote) { //Not using eloquent::when, not a fan of syntactic sugar.
            $stock->value = $updatedQuote;
            $stock->save();
            return redirect('/stocks')->with('success', $stock->ticket . ' value updated.');
        } else {
            return redirect('/stocks')->with('error', 'Stock ticket:' . $stock->ticket . ' not found.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function stockView(Request $request)
    {
        $ticket = $request->ticket;
        //$ticket = $request->
        $jsonurl = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=".$ticket."&apikey=" . env('AVANTAGE_API_KEY');
        $jsonResponse = file_get_contents($jsonurl);
        $Stack_View   = json_decode($jsonResponse,true);
        
        $jsonurl = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=".$ticket."&apikey=".env('AVANTAGE_API_KEY');
        $jsonResponse = file_get_contents($jsonurl);
        $Stack_Global = json_decode($jsonResponse,true);
        
        $Stack['ticket']        = $Stack_View['Symbol'];
        $Stack['company']       = $Stack_View['Name'];
        $Stack['currency']      = $Stack_View['Currency'];
        $Stack['Market']        = $Stack_View['MarketCapitalization'];
        $Stack['exchange']      = $Stack_View['Exchange'];
        $Stack['weekhigh']      = $Stack_View['52WeekHigh'];
        $Stack['weeklow']       = $Stack_View['52WeekLow'];
        $Stack['desc']          = $Stack_View['Description'];


        $Stack['dhigh']   = $Stack_Global['Global Quote']['03. high'];
        $Stack['dlow']    = $Stack_Global['Global Quote']['04. low'];
        $Stack['dopen']   = $Stack_Global['Global Quote']['02. open'];
        $Stack['dclose']  = $Stack_Global['Global Quote']['08. previous close'];
        $Stack['price']    = $Stack_Global['Global Quote']['05. price'];
        
        return $Stack;
    }
}