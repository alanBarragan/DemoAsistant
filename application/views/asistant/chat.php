<style>
.flotante {
    display: scroll;
    position: fixed !important;
    bottom: 20px;
    right: 0px;
    z-index: 1000;
}

.popup-box-on {
    display: block !important;
}

.popup-box {
    background-color: #ffffff;
    border: 1px solid #b0b0b0;
    bottom: 15px;
    display: none;
    height: 460px;
    position: fixed;
    right: 0.1%;
    width: 400px;
    font-family: 'Open Sans', sans-serif;
    z-index: 10000;
}

.row-bordered {
  position: relative;
}

.row-bordered:after {
  content: "";
  display: block;
  border-bottom: 1px solid #ccc;
  position: absolute;
  bottom: 0;
  left: 5px;
  right: 5px;
}
  
</style>
<a class='flotante'>
    <img src='<?=base_url()?>/assets/images/message.png' heigth="50" width="50" />
</a>

<div class="popup-box" id="qnimate">
    <div class="card h-100">
        <div class="card-header h-4">
            Chat
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="card-body border-success h-90" style="overflow-y: scroll;"></div>
        <div class="card-footer h-6 w-100 text-muted" style="bottom: 0;">
            <div class="row">
                <div class="col">
                    <input type="text" id="message" class="form-control chat-input" placeholder="Enter your text">
                </div>
                <div class="col-auto">
                    <button type="submit" id="btnEnviar"
                        class="btn btn-danger chat-send btn-block waves-effect waves-light">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let conversacion_text;
$(function() {
   
    $('.card-body').scrollTop(100000);
    var d = new Date();
    $(".flotante").click(function() {
        $('#qnimate').addClass('popup-box-on');
    });

    $(".close").click(function() {
        $('#qnimate').removeClass('popup-box-on');
    });

    $('#message').keypress(function(event) {
        if (event.keyCode == 13) {
            conversar();
        }
    });

    $("#btnEnviar").click(function() {
        conversar();
    });
    conversar();
    
    function conversar() {
        d = new Date();
        var parametros = {
            user_message: $('#message').val()
        }
        conversacion_text=conversacion_text+" usuario:"+$('#message').val();
        console.log(conversacion_text);
        msgUsuario();                
        $.post('<?= base_url() ?>asistant/asistente', parametros, function(res) {
            
            var respuesta = res.output.text;
            // console.log(respuesta);
            console.log(res);
            nodo = res.output.nodes_visited;
            if(nodo == "node_5_1572018938298"){
                //levanta ticket;
                respuesta = "Estamos levantando un ticket para resolver sus dudas.";
            }
            conversacion_text=conversacion_text+" sistema:"+respuesta;
            //si el nodo de respuesta es levantar un ticket se hace una llamada post
            if(respuesta!=""){
                $(".card-body").append(
                '<div class="container-fluid">'+
                    '<div class="row row-bordered">'+
                        '<div class="col-10">'+
                            '<p class="text-right">'+
                                respuesta +
                                '<p class="text-sm-right">' +
                                '<em>' + d.getHours() + ':' + (Number(d.getMinutes())<10 ? '0' + d.getMinutes() : d.getMinutes()) + '</em>' +
                                '</p>' +
                            '</p>'+
                        ' </div>'+                        
                        '<div class="col-2">'+
                            '<div class="imgAbt">'+
                                '<img width="50" height="50" src="<?= base_url() ?>assets/images/salida.png" />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'
                );
            }
            $('#message').val('');
            $('.card-body').scrollTop(100000);
        });
    } // fin conversa
    function msgUsuario(){
        d = new Date();
        var_temp = $('#message').val();
        if(var_temp!=""){
            $(".card-body").append(
            '<div class="container-fluid">'+
                '<div class="row row-bordered">'+
                    '<div class="col-2">'+
                        '<div class="imgAbt">'+
                            '<img width="50" height="50" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpdX6tPX96Zk00S47LcCYAdoFK8INeCElPeJrVDrh8phAGqUZP_g" />'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-10">'+
                        '<p class="text-left">'+
                            $('#message').val() +
                            '<p class="text-sm-left">' +
                            '<em>' + d.getHours() + ':' + (Number(d.getMinutes())<10 ? '0' + d.getMinutes() : d.getMinutes()) + '</em>' +
                            '</p>' +
                        '</p>'+
                ' </div>'+
                '</div>'+
            '</div>');
        }
        $('.card-body').scrollTop(100000);
    }

})
</script>