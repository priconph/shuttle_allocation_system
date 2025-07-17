<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <!-- CSS LINKS -->
    @include('shared.css_links.css_links')
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h1 {
            color: red;
        }
    </style>
</head>
<body>
    <div class="text-center w-25">
        <h1>Access Denied</h1>
        <p class="">You already have access to this module, but ESS needs to assign your role in the system.</p>
        <p><a href="/" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Return to RapidX</a></p>
    </div>
</body>
</html>