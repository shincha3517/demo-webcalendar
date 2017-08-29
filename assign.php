<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Makeit Schedule</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Schedule timeable <small>Makeit technologies</small></h1>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Step 1</div>
                    <div class="panel-body">
                        <div class="well">
                            <div class="row">
                                <div class="col-xs-6">
                                    <select class="form-control" id="ddUser">
                                        <option>--Please select date first</option>
                                        <option value="1">Weijie</option>
                                        <option value="2">Allan Chua</option>
                                        <option value="3">Zheying Zhang</option>
                                        <option value="4">Vu Dao</option>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-offset-5">
                                            <div id="datepicker" data-date="12/03/2012"></div>
                                            <input type="hidden" id="my_hidden_input" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12" id="step2">
                <div class="panel panel-default">
                    <div class="panel-heading">Step 2</div>
                    <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div id="visualization"></div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12" id="step3">
                <div class="panel panel-default">
                    <div class="panel-heading">Step 3</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <form class="form-horizontal" method="post" action="success.php">
                                    <div class="form-group">
                                        <label for="name" class="col-md-4 control-label">Select Teacher</label>
                                        <div class="col-md-6">
                                            <select class="form-control">
                                                <option value="1">User 1</option>
                                                <option value="2">User 2</option>
                                                <option value="3">User 3</option>
                                                <option value="4">User 4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-md-4 control-label">Send Notification</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" value="email" /> Email
                                            <input type="checkbox" value="email" /> SMS
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-md-4 control-label">Send Notification</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" cols="4" rows="5">Hello {User}
You has recieved invite to handle new job in date {date_format}
Regards,
                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Send
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>





    </div>


</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script src="js/main.js"></script>

</html>