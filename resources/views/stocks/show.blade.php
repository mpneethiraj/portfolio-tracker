@extends('layouts.app')
<style type="text/css">
  #stockIndicator {
  text-align: left;
  padding: 10px;
  margin: 5px;
  color: red;
}

.ajaxtrigger:hover {
  cursor: pointer;
  cursor: hand;
}

#stock_miniQuote_head {
  background-color: #464A55;
  color: #FFFFFF;
  font-size: 14px;
  font-weight: bold;
  padding-bottom: 10px;
  padding-left: 10px;
  padding-right: 10px;
  padding-top: 10px;
}

#stock_miniQuote {
  border-bottom-color: #DDDDDD;
  border-bottom-left-radius: 5px 5px;
  border-bottom-right-radius: 5px 5px;
  border-bottom-style: solid;
  border-bottom-width: 1px;
  border-left-color: #DDDDDD;
  border-left-style: solid;
  border-left-width: 1px;
  border-right-color: #DDDDDD;
  border-right-style: solid;
  border-right-width: 1px;
  border-top-color: initial;
  border-top-style: none;
  border-top-width: initial;
  list-style-type: none;
  margin-bottom: 10px;
  padding-bottom: 0;
  padding-top: 10px;
  vertical-align: text-top;
  height: 100%;
  width: 99%;
}

.stock_divider {
  border-bottom: 1px solid #B2B0AD;
  padding-bottom: 5px;
}

#stock_left {
  float: left;
  width: 35%;
  height: 50px;
  border-right: 1px solid #B2B0AD;
  padding: 0 15px;
}

#stock_right {
  float: right;
  width: *;
  padding: 0 20px;
  vertical-align: text-top;
}

.stock_label {
  font-size: 14px;
}

.stock_strong {
  font-size: 17px;
}

#stock_body {
  padding: 10px 15px 15px 15px;
}

#stock_body_content {
  float: left;
  padding: 0 15px;
}
#duration {
  display:block;
  margin: 0;
  width:60%;
}

datalist {
  display: table !important;
  table-layout: fixed !important; 
  width: 65% !important;
}

option {
  display: table-cell !important;
}
</style>
@section('main')
<div class="row">
  <div class="col-sm-8 offset-sm-2">
    <h2 class="display-4">View Stock Details</h2>
    <div>
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        <br />
      @endif
      <div id="stock_miniQuote_head"><span id="stockSymbol"></span>{{$Stack_View['Symbol']}}&nbsp;({{$Stack_View['AssetType']}})</div>
      <div id="stock_miniQuote">
        <div class="stock_divider">
          <div id="stock_left">
            <span class="stock_label">Price</span><br/>
            <strong class="stock_strong">{{$Stack_View['Currency']}} <span id="stockAsk">{{$Stack_Global['Global Quote']['05. price']}}</span></strong><br/>
          </div>
          <div id="stock_right">
            <span class="stock_label">Company Name</span><br/>
            <strong class="stock_strong"><span id="stockChange">{{$Stack_View['Name']}}</span></strong><br />
          </div>
          <div style="clear: both;"></div>
        </div>
        <div id="stock_body">
          <div class="row">
            <div class="col-md-4">
              <span class="stock_label">Day High</span><br/>
              <strong class="stock_strong">
                <span id="stockVolume">{{$Stack_Global['Global Quote']['03. high']}}</span>
              </strong>
            </div>
            <div class="col-md-4">
              <span class="stock_label">Day Low</span><br/>
              <strong class="stock_strong"><span id="stockAvgVolume">{{$Stack_Global['Global Quote']['04. low']}}</span></strong>
            </div>
            <div class="col-md-4">
              <span class="stock_label">52 Week Range High</span><br/>
              <strong class="stock_strong"><span id="stockRange">{{$Stack_View['52WeekHigh']}}</span></strong>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <span class="stock_label">52 Week Range Low</span><br/>
              <strong class="stock_strong"><span id="stockRange">{{$Stack_View['52WeekLow']}}</span></strong>
            </div>
            <div class="col-md-4">
              <span class="stock_label">Day Open</span><br/>
              <strong class="stock_strong"><span id="stockRange">{{$Stack_Global['Global Quote']['02. open']}}</span></strong>
            </div>
            <div class="col-md-4">
              <span class="stock_label">Day Close</span><br/>
              <strong class="stock_strong"><span id="stockRange">{{$Stack_Global['Global Quote']['08. previous close']}}</span></strong>
            </div>
          </div>
          <div style="clear: both;"></div>
        
        <hr/>
        <h3>Stock Prices</h3><div id="prices"></div>
        <label for="spacing">Duration in Months:</label>
        <div class="slider">
          <datalist id="steplist">
            <option label="3" value="3">3</option>
            <option label="6" value="6">6</option>
            <option label="9" value="9">9</option>
            <option label="12" value="12">12</option>
            <option label="15" value="15">15</option> 
            <option label="18" value="18">18</option>
            <option label="21" value="21">21</option>
            <option label="24" value="24">24</option>
          </datalist>
          <input onchange="displayPrices()" list="steplist" id="duration" type="range" min="3" max="24" step="3" value="12" />  
        </div>
        
        <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
displayPrices();
function displayPrices(){
  
let duration  = $("#duration").val();
let name      = '{{$Stack_View["Symbol"]}}';
var appkey    = "{{env('AVANTAGE_API_KEY')}}";
var xmlhttp   = new XMLHttpRequest(),
    url       = 'https://www.alphavantage.co/query?function=TIME_SERIES_MONTHLY&symbol='+name+'&apikey='+appkey;

xmlhttp.open('GET', url, true);
xmlhttp.onload = function() {
  if (this.readyState == 4 && this.status == 200) {
    json=JSON.parse(this.responseText);
  
    let keys = Object.keys(json['Monthly Time Series']);
    var dates = [];
    var pricesClose = [];
    const months = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", 
           "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
    // get prices for last no. of months
    for (let i=0; i<duration; i++) {
      let key = keys[i];
      pricesClose.push(json['Monthly Time Series'][key]['4. close']);
      dates.push(months[Number(key.slice(5, 7) - 1)] + key.slice(2, 4));
    }
    
    displayChart(name, dates, pricesClose)
  }
};
xmlhttp.send();
}

function displayChart(name, dates, pricesClose) {
  let labels = dates.reverse();
  let data = pricesClose.reverse();
  let ctx = document.getElementById('myChart').getContext('2d');
  let chart = new Chart(ctx, {
      // The type of chart we want to create
      type: 'line',

      // The data for our dataset
      data: {
          labels: labels,
          datasets: [{
              label: name,
              borderColor: 'rgb(255, 99, 132)',
              data: data,
            lineTension: 0,
          }]
      },

      // Configuration options go here
      options: {}
  });
}
</script>
@endsection