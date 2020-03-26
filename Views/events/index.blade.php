@extends(themePath('layouts.app'), ['banner'=>'none'])

@section('content')
<section class="bg-light page-section" id="portfolio">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading text-uppercase">Upcoming Events</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
        <ul class="list-group shadow">

        @foreach($events as $event)
        <!-- list group item-->
        <li class="list-group-item">
          <!-- Custom content-->
          <div class="media align-items-lg-center flex-column flex-lg-row p-3">
            <div class="col-lg-3">
              <a href="/events/{{$event->id}}"><img src="{{asset($event->image)}}" alt="{{$event->name}}" class="img-fluid mr-4"></a>
            </div>
            <div class="col-lg-9">
            <div class="media-body">
              <div class="mb-2">{{$event->display_date}}</div>
              <h5 class="mt-0 font-weight-bold mb-2"><a href="/events/{{$event->id}}">{{$event->name}}</a></h5>
              <p class="text-muted mb-0 small">{{$event->address}}</p>
              <div class="d-flex align-items-center justify-content-between mt-1">
                <span class="text-muted mb-0 small">{{$event->price_display}}</span>
              </div>
            </div>
            </div>
          </div>
          <!-- End -->
        </li>
        @endforeach
        <!-- End -->
      </ul>
    </div>
  </div>
</div>
</section>
@stop

@push('scripts')
<script>
// Collapse Navbar
  //$("#mainNav").addClass("navbar-shrink");
  // var navbarCollapse = function() {
  //   if ($("#mainNav").offset().top > 100) {
  //     $("#mainNav").addClass("navbar-shrink");
  //   } else {
  //     $("#mainNav").removeClass("navbar-shrink");
  //   }
  // };
  // // Collapse now if page is not at top
  // navbarCollapse();
  // Collapse the navbar when page is scrolled
  //$(window).scroll(navbarCollapse);
</script>
@endpush