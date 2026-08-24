<x-mail::message>
# {{ $locale === 'es' ? '¡Bienvenido a Glodaxia Magazine!' : 'Welcome to Glodaxia Magazine!' }}

{{ $locale === 'es' 
    ? 'Gracias por unirte a nuestra comunidad. Para comenzar a recibir nuestros resúmenes tecnológicos de vanguardia, análisis de inteligencia artificial y noticias de ingeniería, por favor confirma tu dirección de correo electrónico haciendo clic en el siguiente botón:' 
    : 'Thank you for joining our readership. To start receiving our high-signal technology intelligence, artificial intelligence analyses, and engineering dispatches, please confirm your email address by clicking the button below:' }}

<x-mail::button :url="$verifyUrl" color="primary">
{{ $locale === 'es' ? 'Confirmar Mi Suscripción' : 'Confirm My Subscription' }}
</x-mail::button>

{{ $locale === 'es'
    ? 'Este paso de doble confirmación (Double Opt-in) nos permite garantizar que nadie use tu correo sin tu consentimiento y mantener una comunidad 100% libre de spam.'
    : 'This double opt-in verification ensures that your email address is never used without your explicit authorization and keeps our community 100% spam-free.' }}

---

{{ $locale === 'es' 
    ? 'Si no has solicitado esta suscripción en Glodaxia, puedes ignorar este mensaje de forma segura y no te enviaremos ningún correo adicional.' 
    : 'If you did not request this subscription on Glodaxia, you can safely ignore this email and no further messages will be sent.' }}

<x-mail::subcopy>
{{ $locale === 'es' ? 'Si tienes problemas con el botón, copia y pega este enlace en tu navegador:' : 'If you are having trouble clicking the button, copy and paste this URL into your web browser:' }}
[{{ $verifyUrl }}]({{ $verifyUrl }})
</x-mail::subcopy>

{{ $locale === 'es' ? 'Atentamente,' : 'Best regards,' }}<br>
**{{ config('app.name') }} Editorial Desk**
</x-mail::message>