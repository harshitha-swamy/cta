@php
    $role = session('role');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
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
                     <div class="reviewContainer">
                          <h3>Start My Deal</h3>
                          <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Task Requirement
                                    </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                           <p>Gitlab Ticket Link: <a href="{{$ticket->ticket_link}}" target="_blank" rel="noopener noreferrer">{{$ticket->ticket_link}}</a></p>
                                           <p>Dealer Code: {{$ticket->dealer_code}}</p>
                                           <p>Created By: {{$ticket->created_by}}</p>
                                           <p>Created At: {{ $ticket->created_at->format('M d, Y') }}</p>
                                           <p>status: {{$ticket->status}}</p>
                                           <p>Task Description: {{$ticket->ticket_description}}</p>
                                           <p>Dealer Website: <a href="{{$ticket->website_link}}" target="_blank" rel="noopener noreferrer">{{$ticket->website_link}}</a></p>
                                           <p>Project Name: {{$ticket->project_name}}</p>
                                        </div>
                                    </div>
                                </div>
                               @if($ticket->comments)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Comments
                                        </button>
                                    </h2>

                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">

                                            <!-- Comment Box -->
                                            <div class="p-3 border rounded bg-white">

                                                <!-- Red heading -->
                                                 @if($role === 2)
                                                <p class="m-0 fw-bold text-danger" style="font-size: 15px;">
                                                    Your CTA is rejected by the Approver!
                                                </p>
                                                @else
                                                <p class="m-0 fw-bold text-danger" style="font-size: 15px;">
                                                    You have rejected this CTA. Your feedback has been submitted.
                                                </p>
                                                @endif
                                               

                                                <!-- User line -->
                                                <div class="d-flex align-items-center mt-2">
                                                    
                                                    <span style="font-size: 14px; color: #6b7280;">
                                                        <strong>{{ $ticket->created_by }}</strong> - 
                                                        {{ $ticket->updated_at->format('m/d/Y h:i A') }}
                                                    </span>
                                                </div>

                                                <!-- Comment text -->
                                                <p class="mt-2 mb-0" style="font-size: 14px; color: #6b7280;">
                                                    “{{ $ticket->comments }}”
                                                </p>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                       Attachments
                                    </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                           <div class="refrence">
                                                <p>Refrence Image</p>
                                                <img src="{{$ticket->reference_image_link}}" alt="Reference Image">
                                           </div>
                                           <div class="newcta">
                                                 <p>New CTA</p>
                                                <img src="{{$ticket->current_image_link}}" alt="Current Image">
                                           </div>
                                           <div class="liveContainer">
                                            <label>Live CTA Link:</label>
                                            <input type="text" value="{{$ticket->current_image_link}}" class="form-control">
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                @if(session('role') === 1)
                                <div class="row">
                                   <div class="col-lg-6">
                                        <!-- <button type="button" class="btn changesBtn">Send for Changes</button>
                                        <button type="button" class="btn approveBtn" data-id="{{ $ticket->id }}">Approve it</button>  -->
                                        <button type="button" class="btn reviewbtn" data-url="{{ $ticket->website_link }}">Go to Review</button>
                                        <button type="button" class="btn changesBtn">Send for Changes</button>
                                        <button type="button" class="btn btn-success approveBtn" data-id="{{ $ticket->id }}">Approve it</button>

                                        <!-- Popup box for comment -->
                                        <div id="changesPopup" style="display:none; margin-top:15px;">
                                            <textarea id="changeComment" class="form-control" placeholder="Enter your comment..." rows="3"></textarea>
                                            <br>
                                            <button type="button" class="btn btn-primary" id="saveChangesBtn">Save</button>
                                            <button type="button" class="btn btn-secondary" id="cancelChangesBtn">Cancel</button>
                                        </div>

                                        <input type="hidden" id="taskId" value="{{ $ticket->id }}">

                                   </div>
                                </div>
                                @endif
                            </div>
                     </div>
                </div>
            </div>
      </section>
      <!-- Modal -->


</body>
</html>


<script>
     $(document).ready(function() {
        
     });

     
$(document).on('click', '.approveBtn', function () {

    let btn = $(this);
    let taskId = btn.data('id');

    $.ajax({
        url: "{{ route('task.approve') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            task_id: taskId
        },
        success: function(res) {
    console.log("RESPONSE:", res); // DEBUG

    if (res.success) {

        btn.text('Approved')
            .prop('disabled', true)
            .removeClass('btn-primary')
            .addClass('btn-success');

    } else {
        alert("Update failed: " + (res.message ?? "No message"));
    }
},
error: function(xhr) {
    console.log("XHR ERROR:", xhr); // DEBUG
    alert("Server error: " + xhr.status + " → " + xhr.responseText);
}

    });

});


$(document).ready(function() {

    // Open the textarea popup
    $('.changesBtn').click(function () {
        $('#changesPopup').slideDown();
    });

    // Cancel button
    $('#cancelChangesBtn').click(function () {
        $('#changesPopup').slideUp();
        $('#changeComment').val("");
    });

    // Save changes
    $('#saveChangesBtn').click(function () {
        let taskId = $('#taskId').val();
        let comment = $('#changeComment').val();

        if(comment.trim() === "") {
            alert("Please enter a comment.");
            return;
        }

        $.ajax({
            url: "/task/send-for-changes/" + taskId,
            method: "POST",
            data: {
                comment: comment,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                alert("Changes submitted successfully!");
                $('#changesPopup').slideUp();
                $('#changeComment').val("");
                $('.changesBtn').text("Changes Requested"); // optional UI update
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Error submitting changes!");
            }
        });
    });

});

$(document).on('click', '.reviewbtn', function () {
    let url = $(this).data('url');

    if (!url) {
        alert("No website link available!");
        return;
    }

    window.open(url, '_blank');
});



</script>


