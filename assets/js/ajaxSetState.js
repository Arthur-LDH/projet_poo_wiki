$(document).ready(function() {
    let inputs = $('input[id^="state_"]')
    console.log(inputs)
    inputs.each(function() {
        console.log($(this).attr('id'))
        $(this).on('change', function() {
            let id = $(this).attr('id').split('_')[1]
            let state = $(this).prop('checked')
            let newState = state ? 1 : 0
            $.ajax({
                url: '/setPublished/' + id + '/' + newState,
                type: 'GET',
                success: function(response) {
                    if (response["success"] === true) {
                        this.checked = !state
                    }
                    else {
                        this.checked = state
                    }


                },
                error: function(error) {
                    console.log(error)
                }
            })
        })
    })
})