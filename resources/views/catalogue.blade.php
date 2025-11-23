<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTA Catalogue</title>

    <!-- Local and CDN styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="https://d1jougtdqdwy1v.cloudfront.net/css/5.2.3/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

    <!-- JS libs -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/5.2.3/bootstrap.bundle.min.js"></script>
</head>

<body>
    @include('header')
        <section class="dashboard-content">
            @include('sidebar')
        <!-- Main Dashboard Content -->
        <div class="main-dashboard-container flex-grow-1 p-4">
            <div class="hero-section-dashboard">
                <div class="catalogueContainer">
                    <h3>CTA Catalogue</h3> 

                    <div class="row mt-4 mb-3 align-items-center">
                        <div class="col-lg-9">
                            <p class="mb-0">Showing: {{ $ctaImages->total() }} CTA results</p>
                        </div>
                        <div class="col-lg-3">
                            <input type="search" id="searchCta" class="form-control" placeholder="Search CTA">
                        </div>
                    </div>

                    <!-- CTA Cards -->
                    <div class="ctaCardWrapper mt-4">
                        <div class="row" id="ctaGrid">
                            @forelse($ctaImages as $cta)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    
                                    <div class="ctaCards text-center ">
                                         <div class="ctaLabel" style="padding-bottom:15px;">CTA - {{ $loop->iteration }}</div>
                                        <img src="{{ $cta->button_image_url }}" alt="CTA Image" class="img-fluid rounded">
                                    </div>
                                </div>
                            @empty
                                <p>No CTA images found.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="ctaPagination text-center mt-4">
                        {{ $ctaImages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Optional: Image Preview Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded" alt="">
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        $(document).ready(function () {
            // Open image in modal on click
            $('.ctaCards img').on('click', function () {
                let src = $(this).attr('src');
                $('#modalImage').attr('src', src);
                $('#imageModal').modal('show');
            });

            // Basic search filter (client-side)
            $('#searchCta').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('#ctaGrid .col-lg-4').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <script>
$(document).ready(function () {
    $('#ctaSearch').on('keyup', function () {
        var value = $(this).val().toLowerCase();

        // Loop through each card and toggle visibility
        $('#ctaGrid .col-lg-4, #ctaGrid .col-md-6').filter(function () {
            // Search both the image URL and text below the image
            let imgText = $(this).find('img').attr('src').toLowerCase();
            let labelText = $(this).find('p').text().toLowerCase();
            $(this).toggle(imgText.indexOf(value) > -1 || labelText.indexOf(value) > -1);
        });
    });
});
</script>

</body>
</html>

