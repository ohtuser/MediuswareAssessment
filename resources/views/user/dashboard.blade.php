@extends('user.layout.master')
@section('title', 'Dashboard')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Dashboard</h1>
                <h3 class="mb-0">User: {{ auth()->guard('auth')->user()->name }}</h3>
                <p class="mb-0">Account Type: {{ auth()->guard('auth')->user()->account_type == 1 ? 'Individual' : 'Business' }}</p>
                <p class="mb-0">Current Balance: {{ $current_balance }}</p>
            </div>

            <table class="mt-2 table table-bordered">
                <tr>
                    <th>Sl</th>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Deposit</th>
                    <th>Withdraw</th>
                    <th>Fee</th>
                    <th>Balance</th>
                </tr>
                @php
                    $balance = 0;
                @endphp
                @foreach ($all_transactions as $tr)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d-M-Y', strtotime($tr->date)) }}</td>
                        <td>{{ $tr->transaction_type == 1 ? 'Deposit' : 'Withdraw' }}</td>
                        <td>{{ $tr->transaction_type == 1 ? $tr->amount : '' }}</td>
                        <td>{{ $tr->transaction_type == 2 ? $tr->amount : '' }}</td>
                        <td>{{ $tr->fee }}</td>
                        @php
                            $balance = $tr->transaction_type == 1 ? $balance + $tr->amount : $balance - $tr->amount;
                            $balance = $balance - $tr->fee;
                        @endphp
                        <td>{{ $balance }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3">Total</th>
                    <th>{{ $all_transactions->where('transaction_type', 1)->sum('amount') }}</th>
                    <th>{{ $all_transactions->where('transaction_type', 2)->sum('amount') }}</th>
                    <th>{{ $all_transactions->where('transaction_type', 1)->sum('fee') }}</th>
                    <th>{{ $balance }}</th>
                </tr>
            </table>
        </div>
    </div>
    

@endsection