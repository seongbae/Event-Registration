@extends(themePath('layouts.app'), ['banner'=>'none'])

@section('content')
<section class="bg-light page-section" id="portfolio">
    <div class="container">
      @if(flash()->message)
            <div class="alert {{ flash()->class }}">
                {{ flash()->message }}
            </div>
        @endif

        <h2 class="blog-post-title">{{$event->name}}</h2>
            <p class="blog-post-meta">{{$event->display_date}}</p>
      <div class="row">
        <div class="col-lg-8">
          <div class="blog-post">
            

            @if ($event->image)
            <img src="{{$event->image}}" class="img-fluid mb-4">
            @endif

            {!! nl2br(e($event->description)) !!}
           
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card flex-md-row mb-4 box-shadow h-md-250">
            <div class="card-body d-flex flex-column align-items-start">
              <strong class="d-inline-block mb-2 text-primary">{{$event->price_display}}</strong>
              <h4 class="mb-0">
                <a class="text-dark" href="#">{{$event->name}}</a>
              </h4>
              <div class="mb-1 text-muted">{{$event->display_date}}</div>
              <p class="card-text mt-3">{{$event->address}}</p>
              @if ($event->external_link != null)
              <a href="{{$event->external_link}}" target="_blank" class="mt-3">Register</a>
              @else
              <a href="#register" class="mt-3">Register</a>
              @endif
            </div>
          </div>
        </div>
      </div>
      <hr>
      @if ($event->external_link != null)
      <a href="{{$event->external_link}}" class="btn btn-primary btn-lg" target="_blank">Register</a>
      @else
      <div class="row" id="register">
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Register</h4>
            <form class="needs-validation" novalidate="" id="paymentform" method="POST" action="/event/register">
              @csrf
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="firstName">First name</label>
                  <input type="text" class="form-control" id="firstName" name="reg_first_name" placeholder="" value="" required>
                  <div class="invalid-feedback">
                    Valid first name is required.
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="lastName">Last name</label>
                  <input type="text" class="form-control" id="lastName" name="reg_last_name" placeholder="" value="" required>
                  <div class="invalid-feedback">
                    Valid last name is required.
                  </div>
                </div>
              </div>
              <div class="row">
               <div class="col-md-6 mb-3">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="reg_email" placeholder="you@example.com" required>
                  <div class="invalid-feedback">
                    Please enter a valid email address.
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone">Phone</label>
                  <input type="phone" class="form-control" id="phone" name="reg_phone" placeholder="703-855-8555" required>
                  <div class="invalid-feedback">
                    Please enter a valid phone number.
                  </div>
                </div>
              </div>
              @if (!$event->free)
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="firstName">Name on Card</label>
                  <input type="text" class="form-control" id="card-holder-name" name="card_name" placeholder="" value="" required="">
                  <div class="invalid-feedback">
                    Valid name is required.
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="firstName">Credit card number</label>
                    <div id="card-element" class="form-control">
                    <!-- A Stripe Element will be inserted here. -->           
                    </div>
                </div>
              </div>
              @endif
              <hr class="mb-4">
              <input type="hidden" name="total" value="{{$event->price}}">
              <input type="hidden" name="event_id" value="{{$event->id}}">
              <button class="btn btn-primary btn-lg" type="submit" id="card-button">Register</button>
            </form>
          </div>
        <div class="col-md-4 order-md-2 mb-4">
          @if (!$event->free)
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span >Your cart</span>
            <span class="badge badge-secondary badge-pill"></span>
          </h4>
          <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Registration</h6>
                <small class="text-muted"></small>
              </div>
              <span class="text-muted">${{$event->price}}</span>
            </li>
            <!-- <li class="list-group-item d-flex justify-content-between bg-light">
              <div class="text-success">
                <h6 class="my-0">Promo code</h6>
                <small>EXAMPLECODE</small>
              </div>
              <span class="text-success">-$5</span>
            </li> -->
            <li class="list-group-item d-flex justify-content-between">
              <span>Total (USD)</span>
              <strong>${{$event->price}}</strong>
            </li>
          </ul>
          @endif
          <!-- <form class="card p-2">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Promo code">
              <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">Redeem</button>
              </div>
            </div>
          </form> -->
        </div>
      </div>
      @endif
    </div>
  </section>
@stop

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>

    var stripe = Stripe('{{option('stripe_public_key')}}');
    var elements = stripe.elements();
    var cardElement = elements.create('card');

    cardElement.mount('#card-element');

    // Add an instance of the card UI component into the `card-element` <div>
    var cardHolderName = document.getElementById('card-holder-name');
    var cardButton = document.getElementById('card-button');

    cardButton.addEventListener('click', async (e) => {
    var { paymentMethod, error } = await stripe.createPaymentMethod(
        'card', cardElement, {
            billing_details: { name: cardHolderName.value }
        }
    );
    if (error) {
        console.log('error');
        } else {
                var payment_id = paymentMethod.id;
                createPayment(payment_id);
        }   
    });

    var form = document.getElementById('paymentform');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
    });

    // Submit the form with the token ID.
    function createPayment(payment_id) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('paymentform');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'payment_id');
    hiddenInput.setAttribute('value',payment_id);
    form.appendChild(hiddenInput);
    // Submit the form

    form.submit();

    }
</script>
@endpush