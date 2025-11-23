<script>
    // Set CSRF token in every AJAX request
    $.ajaxSetup({

    headers: {

        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

    }

});

 
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


     });

$('#ticketForm').on('submit', function (e) {

    e.preventDefault(); // prevent normal form submit
 
    let formData = $(this).serialize();
 
    console.log("Form Data:", formData); // verify it's not empty
 
    $.ajax({

        url: "{{ route('tickets.store') }}",

        type: "POST",

        data: formData,

        success: function (response) {

            alert("Ticket saved successfully!");

            $('#add-task-form').modal('hide');

            location.reload();

        },

        error: function (xhr) {

            console.log(xhr.responseText);

            alert('Error: ' + xhr.responseJSON.message);

        }

    });

});

 

</script>

<script>
$(document).ready(function() {
    $('.status-dropdown').change(function() {
        let ticketId = $(this).data('id');
        let newStatus = $(this).val();

        $.ajax({
            url: '/update-status/' + ticketId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                alert('Status updated successfully!');
            },
            error: function(xhr) {
                alert('Error updating status.');
            }
        });
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const pairs = [
            { color: 'bgColor', text: 'bgColorText' },
            { color: 'textColor', text: 'textColorText' },
            { color: 'borderColor', text: 'borderColorText' }
        ];

        pairs.forEach(({ color, text }) => {
            const colorInput = document.getElementById(color);
            const textInput = document.getElementById(text);

            colorInput.addEventListener('input', function () {
                textInput.value = this.value;
            });
        });
    });
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const dealerInput = document.getElementById('DealerCode');
    const websiteInput = document.getElementById('website');

    dealerInput.addEventListener('blur', function() { // triggers when user leaves the input
        const dealerCode = this.value.trim();

        if (dealerCode !== '') {
            fetch(`{{ route('dealer.getWebsite') }}?dealer_code=${dealerCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.website_link) {
                        websiteInput.value = data.website_link;
                    } else {
                        websiteInput.value = '';
                        alert('No website found for this dealer code.');
                    }
                })
                .catch(error => console.error('Error fetching website:', error));
        }
    });
});
</script>