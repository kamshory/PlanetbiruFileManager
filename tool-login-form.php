<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planetbiru File Manager</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="../static/images/16x16.png" type="image/jpeg" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>
body { background-color: #f8f9fa; }
.login-container { max-width: 400px; margin: 100px auto; }
.card-title { text-align: center; }
.footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #6c757d; }
</style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Planetbiru File Manager</h3>
                <form id="form1" name="form1" method="post" action="login.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" autocomplete="off" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="off" required />
                    </div>
                    <input type="hidden" name="ref" id="ref" value="<?php echo htmlspecialchars(strip_tags($_SERVER['REQUEST_URI']));?>" />
                    <button type="submit" name="login" id="login" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>
        <div class="footer">
            &copy; <a href="http://www.planetbiru.net">Planetbiru Studio</a> 2010-<?php echo date('Y');?>. All rights reserved.
        </div>
    </div>
</div>
</body>
</html>
