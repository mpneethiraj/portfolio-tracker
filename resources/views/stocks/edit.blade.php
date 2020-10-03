@extends('layouts.app') 
@section('main')
<div class="row">
    <div class="col-sm-8 offset-sm-2">
        <h1 class="display-3">Editing Stock</h1>
 
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
        <form method="post" action="{{ route('stocks.update', $stock->id) }}">
            @method('PATCH') 
            @csrf
            <div class="form-group">
 
                <label for="stock_name">Stock Name:*</label>
                <input type="text" class="form-control" name="stock_name" value="{{ $stock->stock_name }}" />
            </div>
 
            <div class="form-group">
                <label for="ticket">Stock Ticket:*</label>
                <input type="text" class="form-control" name="ticket" value="{{ $stock->ticket }}" />
            </div>
 
            <div class="form-group">
                <label for="value">Stock Value:</label>
                <input type="text" class="form-control" name="value" value="{{ $stock->value }}" />
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection