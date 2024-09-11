@extends('layouts.default')

@section('content')
    {{-- @if($key !== env('EQUALPARTNER_API_KEY'))
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 mt-10">
            <div class='text-center text-xl text-red-600'>    
                key is not validated!
            </div>
        </div>
    @else --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- We've used 3xl here, but feel free to try other max-widths based on your needs -->
        <div class="mx-auto max-w-xl border-2 mt-10 p-5 rounded-xl">
            <div>
                <img src='https://equalpartners.app/wp-content/uploads/2022/05/Carter-Jim-Equal-Partners-Logo_LR.png' class='h-10 mb-10'/>
            </div>
            <div class='text-lg font-bold'>Change your password</div>

            <!-- Success and error messages -->
            @if(session('success'))
                <div class="text-green-500">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="text-red-500">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('passwordchange') }}" method="post">
                @csrf
                <input type='hidden' name='email' value='{{ $email }}'/>
                <input type='hidden' name='key' value='{{ $key }}'/>

                <div class='rounded-full w-[100%] overflow-hidden bg-slate-200 px-4 py-2 mt-3'>
                    <input name='password' type='password' placeholder="New password" required minlength="6" class='bg-slate-200'/>
                </div>

                <div class='rounded-full w-[100%] overflow-hidden bg-slate-200 px-4 py-2 mt-3'>
                    <input name='confirm_password' type='password' placeholder="Confirm password" required minlength="6" class='bg-slate-200'/>
                </div>

                <div class="mt-10">
                    <button type="submit" class="block w-full rounded-md bg-blue-500 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- @endif --}}
    @endsection