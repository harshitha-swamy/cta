
@php
    $role = session('role');
    
   
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Approver Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
     <link rel="stylesheet" type="text/css" href="https://d1jougtdqdwy1v.cloudfront.net/css/5.2.3/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
     <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/5.2.3/bootstrap.bundle.min.js"></script>

</head>   


    <body>
        
            @include('header')
            <section class="dashboard-content">
                    @include('sidebar')
                    <div class="main-dashboard-container">
                        <div class="hero-section-dashboard">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h2>Welcome Back, John</h2>
                                    <p>Here is your assigned CTA tasks</p>
                                </div>
                                @if($role === 2)
                                <div class="col-lg-6 d-flex justify-content-end align-items-center">
                                    <div class="new-cta-button">
                                        <a href="{{ route('task.create') }}" class="btn-new-cta">+ New Task</a>
                                    </div>

                                </div>
                                @endif
                            </div>
                            <div class="tableContainer">
                                <table id="ctaListTable" class="table-responsive" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Task ID</th>
                                            <th>Dealer Code</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    @if(isset($tickets) && count($tickets) > 0)
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                        <tr>
                                        <?php
                                            $link = $ticket->ticket_link;
                                            $lastNumber = basename($link); 
                                        ?>
                                        <td>#{{ $lastNumber}}</td>
                                        <td>{{ $ticket->dealer_code }}</td>
                                        <td>{{ $ticket->created_by ?? 'Unassigned' }}</td>
                                        <!-- <td>{{$ticket->created_at}}</td>     -->
                                        <td>{{ $ticket->created_at->format('M d, Y') }}</td> 
                                        <td>                                       
                                        <a href="#" class="status-approved">{{$ticket->status}}</a>
                                        </td> 
                                        
                                        <td>
                                            @if($ticket->status === 'In Progress' || $ticket->status === 'Reopen')
                                            <!-- <div class="action-icons">
                                                <a href="#" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div> -->
                                            <div class="action-icons">
                                                <form action="{{ route('developer.edit') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $ticket->id }}">
                                                    <input type="hidden" name="action" value="edit">

                                                    <button type="submit" style="background:none;border:none;padding:0;">
                                                        <i class="bi bi-pencil" title="Edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @elseif($ticket->status === 'In review')
                                            <!-- <div class="action-icons">
                                                <a href="{{route('approver.review',['id' => $ticket->id, 'action' => 'reuse'])}}" title="View"> 
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div> -->
                                            <div class="action-icons">
                                                <form action="{{ route('approver.review') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $ticket->id }}">
                                                    <input type="hidden" name="action" value="reuse">

                                                    <button type="submit" style="background:none;border:none;padding:0;">
                                                        <i class="bi bi-eye" title="View"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @elseif($ticket->status === 'Approved')
                                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>


    
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
            </section>

           
        
    </body>
</html>

<script>
    $(document).ready(function() {
            new DataTable('#ctaListTable');

            $('#ctaListTable_filter input').attr('placeholder', 'Search Button ID');

            $('#ctaListTable_filter label').contents().filter(function() {
                return this.nodeType === 3; // Node.TEXT_NODE
            }).remove();
            
            
            $( function() {
                $( "#startDate" ).datepicker();
                $( "#endDate" ).datepicker();
            } );


             const selectionDiv = `
                <div class="eshopSelection">
                    <div class="selectionHeading">
                        <h4>CTA Tasks</h4>
                    </div>
                    <div class="selectionRadioButtons">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="eshop" value="us" id="radioDefault1US" {{ session('db_connection') === 'mysql2' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radioDefault1US">E-Shop US</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="eshop" value="ca" id="radioDefault2CA" {{ session('db_connection') === 'mysql' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radioDefault2CA">E-Shop Canada</label>
                        </div>
                    </div>
                </div>`;


           const $filter = $('.dataTables_filter');

        // Wrap both inside a flex parent
        $filter.add(selectionDiv).wrapAll('<div class="ctaFilterWrapper"></div>');
     });

       // When selection changes
    $(document).on('change', 'input[name="eshop"]', function() {
        let shop = $(this).val(); // <-- match controller param

        $.ajax({
            url: '/select-eshop',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                shop: shop // <-- match controller input name
            },
            success: function(response) {
                console.log(response.message);
                // You could also reload data or page here if needed
                location.reload(); // Optional: reload to reflect session DB change
            }
        });
    });


   
</script>


