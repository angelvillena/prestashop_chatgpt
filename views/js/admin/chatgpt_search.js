$(document).ready(function() {
    console.log('chatgpt_search.js loaded');
    // on submit #form_chatgpt
    $('#form_chatgpt').on('submit', function(e) {
        e.preventDefault();
        //var form_data = $(this).serialize();
        var prompt = $('#prompt').val();
        var simle_data = $('#simple_data').val();
        askChatGPT(prompt, simle_data);
    });

    function askChatGPT(prompt, simle_data, intento = 0) {
        var max_intentos = 3;
        intento = intento + 1;
        $('#loading').show(); 
        $.ajax({
            url: chatgpt_search_url_ajax,
            type: 'POST',
            data: {
                action: 'chatgptSearch',
                ajax: true,
                prompt: prompt,
                simple_data: simle_data
            },
            success: function(data) {
                var response = JSON.parse(data);
                //console.log('response:', response);
                if (response.success == false && intento < max_intentos) {
                    askChatGPT(prompt, simle_data, intento);
                }
                if (response.output != '') {
                    $('#chatgpt_results').html(response.output);
                }
                //$('#tries').html('Intentos necesitados: ' + intento);
                $('#loading').hide();
            },
            error: function(xhr, status, error) {
                console.log('ajax.php error:', error);
                $('#loading').hide();
            }
        });        
    }

});