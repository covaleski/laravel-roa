@use('Covaleski\LaravelRoa\Facades\Resource')

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ config('app.name') }}</title>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
    </head>
    <body>
        <h1>{{ config('app.name') }}</h1>
        <h2>Application Resources</h2>
        <p>The following resources were mapped:</p>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Model</th>
                </tr>
            </thead>
            <tbody>
                @foreach (Resource::all() as $resource)
                    <tr>
                        <td>{{ $resource->name }}</td>
                        <td>{{ $resource->model }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
