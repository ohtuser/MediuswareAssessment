@extends('user.layout.master')
@section('title', 'Deposit')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Deposit List') }}</div>
                    <div class="card-body">
                        <table class="mt-2 table table-bordered">
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                            @php
                                $balance = 0;
                            @endphp
                            @foreach ($deposit_transactions as $tr)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date('d-M-Y', strtotime($tr->date)) }}</td>
                                    <td>{{ $tr->amount }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2">Total</td>
                                <td>{{ $deposit_transactions->sum('amount') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">{{ __('Deposit') }}</div>
                    <div class="card-body">
                        @include('session_messages')
                        <form action="{{ route('deposit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="any" class="form-control" id="amount" name="amount">
                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Deposit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection