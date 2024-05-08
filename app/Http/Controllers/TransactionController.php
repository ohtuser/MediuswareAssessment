<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function dashboard(){
        $all_transactions = Transaction::get();
        $current_balance = User::find(auth()->guard('auth')->user()->id)->balance;
        return view('user.dashboard', ['all_transactions' => $all_transactions, 'current_balance' => $current_balance]);
    }
    public function deposit(Request $request){
        if($request->isMethod('get')){
            $deposit_transactions = Transaction::where('transaction_type', 1)->get();
            return view('user.transactions.deposit', ['deposit_transactions' => $deposit_transactions]);
        }else{
            $request->validate([
                'amount' => 'required',
            ]);
            try{
                DB::beginTransaction();
                $transaction = new Transaction();
                $transaction->user_id = auth()->guard('auth')->user()->id;
                $transaction->transaction_type = 1;
                $transaction->amount = $request->amount;
                $transaction->date = date('Y-m-d');
                $transaction->created_at = date('Y-m-d H:i:s');
                $transaction->save();

                $user = User::findOrFail(auth()->guard('auth')->user()->id);
                $user->balance += $request->amount;
                $user->save();
                DB::commit();
                return redirect()->route('deposit')->with('success', 'Deposit Transaction Completed');
            } catch(\Exception $e){
                DB::rollBack();
                return redirect()->route('deposit')->with('error', $e->getMessage());
            }
        }
    }
    public function withdraw(Request $request){
        if($request->isMethod('get')){
            $withdraw_transactions = Transaction::where('transaction_type', 2)->get();
            return view('user.transactions.withdraw', ['withdraw_transactions' => $withdraw_transactions]);
        }else{
            $request->validate([
                'amount' => 'required',
            ]);
            try{
                $account_type = auth()->guard('auth')->user()->account_type;
                $user = User::findOrFail(auth()->guard('auth')->user()->id);

                $rate = $account_type == 1 ? 0.015 : 0.025;
                $transaction = new Transaction();
                $transaction->user_id = auth()->guard('auth')->user()->id;
                $transaction->transaction_type = 2;
                $transaction->amount = $request->amount;
                $transaction->date = date('Y-m-d');
                $transaction->created_at = date('Y-m-d H:i:s');
                
                $fee = 0;
                $fee_able_amount = $request->amount - 1000;
                if(date('l') != 'Friday' && $fee_able_amount > 0){
                    $this_month_withdraw = Transaction::where('user_id', auth()->guard('auth')->user()->id)->whereMonth('created_at', date('m'))->where('transaction_type', 2)->sum('amount');
                    if($this_month_withdraw + $request->amount <= 5000){
                        $fee_able_amount = 0;
                    } else if($this_month_withdraw < 5000 && ($this_month_withdraw + $fee_able_amount) > 5000){
                        $fee_able_amount = $this_month_withdraw + $fee_able_amount - 5000;
                    }
                }
                if($account_type == 2){
                    $total_withdraw = Transaction::where('user_id', auth()->guard('auth')->user()->id)->where('transaction_type', 2)->sum('amount');
                    if($total_withdraw + $request->amount > 50000){
                        $rate = 0.015;
                    }
                }
                if($fee_able_amount > 0){
                    $fee = ($rate * $fee_able_amount) / 100;
                }
                
                $transaction->fee = $fee;
                if($fee + $request->amount > $user->balance){
                    DB::rollBack();
                    return redirect()->route('withdraw')->with('error', 'Insufficient Balance');
                }

                $transaction->save();
                
                $user->balance -= $request->amount;
                $user->balance -= $fee;
                $user->save();
                return redirect()->route('withdraw')->with('success', 'Withdraw Transaction Completed');
            } catch(\Exception $e){
                return redirect()->route('withdraw')->with('error', $e->getMessage());
            }
        }
    }
}
