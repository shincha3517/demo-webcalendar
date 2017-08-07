<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Send SMS to available user</h2>
    <form action="/send-sms" method="post">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group">
            <label for="email">Available User:</label>
            <select id="select_users" class="form-control" name="userId">
                <option value="" selected="selected">Please choose user</option>
                <option value="1">Weijie</option>
                <option value="2">Allan</option>
                <option value="3">Zzang</option>
            </select>
        </div>

        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>

</body>
</html>