<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Numbers App</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.css">
    <!-- Custom styles for this template -->
    <link href="css/index.css" rel="stylesheet">

    <!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- Angular Material requires Angular.js Libraries -->
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-messages.min.js"></script>

      <!-- Angular Material Library -->
      <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.js"></script>

    <script src="src/js/main.js" type="text/javascript"></script>

</head>

<body ng-app="numbersApp">
    <div ng-controller="MainController">
        <div class="row">
            <div class="col-md-1"></div>

            <div class="col-md-5" ng-controller="luckyNumbers">
                <h1 class="cover-heading">The Numbers App</h1>
                <p>Lucky Numbers: {{luckyNumbers}}</p>
            </div>
            <div class="col-md-5">
                <form layout-padding ng-cloak>
                  <div class="form-row">
                    <div class="col">
                      <md-datepicker ng-model="start" md-placeholder="From" ng-blur=""></md-datepicker>
                    </div>
                    <div class="col">
                      <md-datepicker ng-model="end" md-placeholder="To" ng-blur=""></md-datepicker>
                    </div>
                    <div class="col">
                      <input type="text" class="form-control" ng-model="limit" placeholder="Limit" ng-blur="emitEvent()"></input>
                    </div>
                    <div class="col">
                        <button type="button" class="form-control" ng-blur="fire()">SUBMIT</button>
                    </div>
                  </div>
                </form>
            </div>

            <div class="col-md-1"></div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-1"></div>

            <div class="col-md-10">
                <h3>Last Few Draws</h3>
                <p class="lead">The numbers should be broken down below.</p>
                <table ng-controller="ListDrawsController" class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td>Date</td>
                            <td>Num(1)</td>
                            <td>Num(2)</td>
                            <td>Num(3)</td>
                            <td>Num(4)</td>
                            <td>Num(5)</td>
                            <td>Num(PB)</td>
                        </tr>
                        <tr ng-repeat="d in draws">
                            <td>{{d.date}}</td>
                            <td ng-repeat="n in d.num">{{n}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-1"></div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-5">
                <h3>Top Numbers</h3>
                <table ng-controller="ListTopNumbers" class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td>Number</td>
                            <td>Dates</td>
                            <td>Count</td>
                        </tr>
                        <tr ng-repeat="t in tops | orderBy:'-count'">
                            <td>{{t.num}}</td>
                            <td>{{t.dates}}</td>
                            <td>{{t.count}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-5">
                <h3>Top Powerball Numbers</h3>
                <table ng-controller="ListTopPbs" class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td>Number</td>
                            <td>Dates</td>
                            <td>Count</td>
                        </tr>
                        <tr ng-repeat="t in pbs | orderBy:'-count'">
                            <td>{{t.num}}</td>
                            <td>{{t.dates}}</td>
                            <td>{{t.count}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
</body>

</html>
