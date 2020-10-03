@extends('layouts.app')

@section('main')
<div class="row">
<div class="col-sm-12">
    <h1 class="display-4">Stocks List</h1>
    <div class="col-md-6">
      <a href="{{ route('stocks.create')}}" class="btn btn-primary mb-3">Add Stock</a>
    </div>
    @if(session()->get('success'))
    <div class="alert alert-success">
      {{ session()->get('success') }}  
    </div>
  @endif
  <table class="table table-striped">
    <thead>
        <tr>
          <td>ID</td>
          <td>Stock Name</td>
          <td>Stock Ticket</td>
          <td>Stock Value</td>
          <td>Updated at</td>
          <td colspan = 4>Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($stocks as $stock)
        <tr>
            <td>{{$stock->id}}</td>
            <td>{{$stock->stock_name}} </td>
            <td>{{$stock->ticket}}</td>
            <td>{{$stock->value}}</td>
            <td>{{$stock->updated_at}}</td>
            <td>
                <a href="{{ route('stocks.edit',$stock->id)}}" class="btn btn-primary">Edit</a>
            </td>
            <td>
                <form action="{{ route('stocks.updateQuote', $stock->id)}}" method="post">
                  @csrf
                  @method('POST')
                  <button class="btn btn-primary" type="submit">Refresh</button>
                </form>
            </td>
            <td>
                <button class="btn btn-primary" type="button" id="live-view-{{$stock->ticket}}">View</button>
            </td>
            <td>
                <form action="{{ route('stocks.destroy', $stock->id)}}" method="post">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
  </table>
  <div id="overlay">
    <div class="cv-spinner">
      <span class="spinner"></span>
    </div>
</div>
<div>
<!-- Modal -->
  <div class="modal fade bd-example-modal-lg" id="stock-view" tabindex="-1" role="dialog" aria-labelledby="stock-view" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" >Stock Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <span>Ticket</span><br/>
              <strong><span id="ticket"></span></strong>
            </div>
            <div class="col-md-6">
              <span>Company Name</span><br/>
              <strong><span id="cname"></span></strong>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <span>Day high</span><br/>
              <strong><span id="d-high"></span></strong>
            </div>
            <div class="col-md-4">
              <span>Day Low</span><br/>
              <strong><span id="d-low"></span></strong>
            </div>
            <div class="col-md-4">
              <span>52 week high</span><br/>
              <strong><span id="w-high"></span></strong>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <span>52 week Low</span><br/>
              <strong><span id="w-low"></span></strong>
            </div>
            <div class="col-md-4">
              <span>Day Open</span><br/>
              <strong><span id="d-open"></span></strong>
            </div>
            <div class="col-md-4">
              <span>Day Close</span><br/>
              <strong><span id="d-close"></span></strong>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <span>Latest price</span><br/>
              <strong><span id="price"></span></strong>
            </div>
            <div class="col-md-4">
              <span>Exchange</span><br/>
              <strong><span id="exchange"></span></strong>
            </div>
            <div class="col-md-4">
              <span>Market Cap</span><br/>
              <strong><span id="mark-cap"></span></strong>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-12">
              <span>About Company</span><br/>
              <span id="desc"></span>
            </div>
            
          </div>
        
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Buy</button>
          <button type="button" class="btn btn-primary">Sell</button>
      </div>
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">

  $(document).ajaxSend(function() {
    $("#overlay").fadeIn(300);　
  });
  $(document).on('click', '[id^="live-view-"]', function(){
        $('#stock-view').trigger('ajaxSend');
        var ticket = $(this)[0].id;
            ticket = ticket.substring(10);
           _token  = $("input[name=csrf-token]").val();
        $.ajax({ 
            url : '{{url("stock-view")}}',
            type: 'get',
            data: {'ticket':ticket , '_token': _token}
        }).done(function(data) {

            $("#ticket").text(data.ticket);
            $("#cname").text(data.company);
            $("#mark-cap").text(data.Market);
            $("#d-high").text(data.dhigh);
            $("#d-low").text(data.dlow);
            $("#w-high").text(data.weekhigh);
            $("#w-low").text(data.weeklow);
            $("#d-open").text(data.dopen);
            $("#d-close").text(data.dclose);
            $("#price").text(data.price);
            $("#exchange").text(data.exchange);
            $("#desc").text(data.desc);
            setTimeout(function(){
              $("#overlay").fadeOut(300);
            },500);
            $("#stock-view").modal('toggle');
        }).fail(function() {
            console.log('Failed');
        });
  });
  
</script>
@endsection
