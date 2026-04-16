<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Users</title>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
    </head>
    <body>
        <h2>Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Models\User::all() as $user)
                    <tr>
                        <td><a href="/users/{{ $user->id }}">{{ $user->id }}</a></td>
                        <td>{{ $user->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
