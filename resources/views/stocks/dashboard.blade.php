@extends('layouts.app')
@section('main')
<style type="text/css">
    .mainSearch{
         width: 50%;
        margin: 50px auto;
    }
    .mainSearchTable{
         width: 80%;
        margin: 50px auto;
    }
    .searchBox-fakeInput {
        background: white;
        border: 1px solid #d6dadc;
        border-radius: 3px;
        display: table;
    
    }

    .searchBox-fakeInput.is-focussed {
      border: 2px solid #007bff42 !important;
    }
    .searchBox-inputWrapper,
    .searchBox-clearWrapper {
        width: 100%;
        display: table-cell;
        vertical-align: middle;
    }
    .searchBox-input {
        background-color: transparent;
        border: none;
      box-shadow: none;
      outline: none;
        width: 100%;
        padding: 0.5rem;
        font-size: inherit;
    }
    .searchBox-input:focus {
      outline: none;
        background: #FFF;
      box-shadow: none;
    }
    .searchBox-clearWrapper {
      padding-right: 0.5rem;
    }
    .searchBox-clear {
        color: #CCC;
        padding: 0;
        cursor: pointer;
        font-size: inherit;
        cursor: pointer;
        line-height: 1.5;
        -webkit-transition: all 3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }
    .searchBox-clearInput:hover {
        color: #AAA;
    }
</style>
<div class="mainSearch " >
    <div class="searchBox-fakeInput">
        <div class="searchBox-inputWrapper">
            <input type="text" class="form-control searchBox-input js-searchBox-input" placeholder="Search">
        </div>
        <div class="searchBox-clearWrapper">
            <span class="searchBox-clear js-clearSearchBox"><i class="fa fa-times-circle"></i></span>
        </div>
    </div>

</div>
<div class="col-md-12 mainSearchTable">
<table class="table table-striped" id="search-table" style="display: none">
    <thead>
        <tr>
          <td>Stock Ticket</td>
          <td>Stock Name</td>
          <td>Stock Currency</td>
          <td colspan = 4>Actions</td>
        </tr>
    </thead>
</table>
</div>
<script type="text/javascript">
    $('.js-clearSearchBox').css('opacity', '0');

    $('.js-searchBox-input').focus(function() {
      $('.searchBox-fakeInput').toggleClass("is-focussed");
    });

    $('.js-searchBox-input').keyup(function() {
      if ($(this).val() !='' ) {
        $('.js-clearSearchBox').css('opacity', '1');
      } else {
        $('.js-clearSearchBox').css('opacity', '0');
      };
      
      $(window).bind('keydown', function(e)  {
        if(e.keyCode === 27) {
          $('.js-searchBox-input').val('');
        };
      });
    });
    // click the button 
    $('.js-clearSearchBox').click(function() {
      $('.js-searchBox-input').val('');
      $('.js-searchBox-input').focus();
      $('.js-clearSearchBox').css('opacity', '0');
    });
    $(document).ready(function(){
        $(".searchBox-input").on("input", function(){
            var ticker = $(this).val();
            var appkey = "{{env('AVANTAGE_API_KEY')}}";
            if(ticker != ''){
                $.ajax({ 
                    url: "https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords="+ticker+"&apikey="+ appkey,
                    type: 'get',
                    datatype:'json'
                }).done(function(responseData) {
                    $.each(responseData.bestMatches, function( index, value ) {
                    
                    var symbol = value['1. symbol'];
                    var url = '{{ route("stocks.show", ":id") }}';
                    url = url.replace(':id', symbol);

                    $("#search-table").append("<tbody><tr><td>" +symbol + "</td><td>" +value['2. name'] + "</td><td>" +value['8. currency'] + "</td><td>"  + "</td><td><form action="+url+" method='get'><button class='btn btn-primary' type='submit'>Over View</button></form></td></tr></tbody");

                    });
                    $("#search-table").show();
                }).fail(function() {
                    console.log('Failed');
                });
            }else{
                alert('Please enter the text to search');
            }
            
        });
    });
</script>
@endsection
