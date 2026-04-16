<!DOCTYPE html>
<html lang="en">
    <head>
        <title>User</title>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
    </head>
    <body>
        @if ($user = App\Models\User::find($user))
            <h2>User</h2>
            <dl>
                <dt>ID</dt>
                <dd>{{ $user->id }}</dd>
            </dl>
            <dl>
                <dt>Name</dt>
                <dd>{{ $user->name }}</dd>
            </dl>
            <dl>
                <dt>E-mail</dt>
                <dd>{{ $user->email }}</dd>
            </dl>
        @else
            <p>User not found.</p>
        @endif
    </body>
</html>
