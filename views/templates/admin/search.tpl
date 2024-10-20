<div id="chatpgt_search">
    <h1>ChatGPT Pruebas</h1>
    <form method="post" action="" id="form_chatgpt">
        <label for="prompt">Qué quieres saber de tu tienda PrestaShop:</label><br>
        <textarea id="prompt" name="prompt" rows="4" cols="50"></textarea><br>
        <input type="checkbox" id="simple_data" name="simple_data" value="1">
        <label for="simple_data">Datos simplificados</label><br>
        <input type="submit" value="Enviar">
    </form>
    <div id="tries" class="mt-4 mb-2"></div>
    <div id="chatgpt_results"></div>    
    <div id="loading">
        <div class="loading_container"><span class="loader"></span></div>
    </div>
</div>