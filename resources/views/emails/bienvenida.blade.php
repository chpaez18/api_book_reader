<body>
<div class="email-container">
    <center><img src="{{ asset('img/empresa/logo_long.png') }}" style="display: block; border: 0px;"></center>
    <br>
    <center><h1 style="font-size: 48px; font-weight: 400; margin: 2; color: #FFB300">¡Bienvenid@ a 100 citas!</h1></center>

    <center>
        <h3 style="color: #142B51" >¡Hola y bienvenido/a a 100 citas.
            <br>
            A continuación te mostramos tu código de compra:

            <?= $data ?>
        </h3>
    </center>
</div>
</body>
<br>
<br>
<br>
<footer>
    <center>Footer</center>
</footer>

<style>
    /* Estilos adicionales (opcional) */
    .email-container {
        padding: 20px;
    }
</style>