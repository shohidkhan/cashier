<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />


    </head>
    <body >


        <nav class="bg-white border-gray-200 dark:bg-gray-900">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Flowbite</span>
                </a>
                <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
                <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                    <a href="{{ url('/') }}" class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-white" aria-current="page">Home</a>
                    </li>
                    <li>
                    <a href="{{ route('plans.index') }}" class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-white" :active="request()->routeIs('plans.index')" aria-current="page">Plans</a>
                    </li>
                    @auth
                        <li>
                    <a href="{{ route('dashboard') }}" class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-white" aria-current="page">Dashboard</a>
                    </li>
                    @endauth

                </ul>
                </div>
            </div>
        </nav>
        <section class="bg-white 00">
            <div class="py-8 px-4  lg:py-16 lg:px-6 dark:bg-gray-900">
                @if (session('error'))
                    <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <div class=" text-center mb-8 lg:mb-12">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Checkout</h2>
                </div>
            </div>

            <div class="max-w-screen-md mx-auto p-4 mt-10">
                <form action="" method="POST" id="form-element">
                    @csrf
                <div>
                    <label for="name"> Name of card</label>
                    <input type="text" id="name" name="name" class="block w-full p-2.5 mb-4 text-sm rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500  dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="John Doe" required>
                </div>
                <div>
                        <label for="card-number"> Card details</label>
                    <div id="card-element" class="block w-full p-2.5 mb-4 text-sm rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500  dark:border-gray-600 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">

                    </div>
                </div>

                {{-- button to pay --}}
                    <button type="submit" id="form-btn" data-secret="{{ $intent->client_secret }}" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Pay</button>
                </form>
            </div>
        </section>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe=Stripe('{{ config('services.stripe.stripe_key') }}');
            // console.log(stripe);
             // Create an instance of Elements.`
            const elements = stripe.elements();
             const cardElement = elements.create('card');
               cardElement.mount('#card-element');

            const form = document.getElementById('form-element');
            const cardButton = document.getElementById('form-btn');
            form.addEventListener('submit',async(e)=>{
                e.preventDefault();
                cardButton.disabled=true;
                // console.log('card-element');
                const {setupIntent, error} = await stripe.confirmCardSetup(
                    cardButton.dataset.secret,{
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: document.getElementById('name').value
                            }
                        }
                    }
                );

                // console.log(error);
                // console.log(setupIntent);

                if(error){
                    cardButton.disabled=false;
                    alert(error.message);
                }else{
                    console.log(setupIntent);

                    const tokenInput = document.createElement('input');
                    tokenInput.setAttribute('type', 'hidden');
                    tokenInput.setAttribute('name', 'token');
                    tokenInput.setAttribute('value', setupIntent.payment_method);
                    form.appendChild(tokenInput);

                    form.submit();
                }
            })
        </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    </body>
</html>
