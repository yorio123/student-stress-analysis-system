

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - FuzzyStress Analysis</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        }

        .login-box{
            width:420px;
            background:#fff;
            border-radius:25px;
            padding:45px;
            box-shadow:0 20px 60px rgba(0,0,0,.2);
            animation:fadeIn .6s ease;
        }

        .logo{
            text-align:center;
            margin-bottom:25px;
        }

        .logo i{
            font-size:60px;
            color:#667eea;
            margin-bottom:10px;
        }

        .logo h2{
            color:#333;
            font-size:28px;
        }

        .logo p{
            color:#777;
            font-size:14px;
            margin-top:5px;
        }

        .input-group{
            margin-bottom:20px;
        }

        .input-group label{
            display:block;
            margin-bottom:8px;
            font-weight:500;
            color:#555;
        }

        .input-box{
            position:relative;
        }

        .input-box i{
            position:absolute;
            left:18px;
            top:50%;
            transform:translateY(-50%);
            color:#667eea;
        }

        .input-box input{
            width:100%;
            padding:15px 20px 15px 48px;
            border:2px solid #e8ecff;
            border-radius:15px;
            background:#fafbff;
            font-size:15px;
            transition:.3s;
        }

        .input-box input:focus{
            outline:none;
            border-color:#667eea;
            background:#fff;
            box-shadow:0 0 0 3px rgba(102,126,234,.15);
        }

        .remember{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
            font-size:14px;
        }

        .remember a{
            color:#667eea;
            text-decoration:none;
        }

        .btn-login{
            width:100%;
            border:none;
            padding:15px;
            border-radius:50px;
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:#fff;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
            box-shadow:0 10px 25px rgba(102,126,234,.35);
        }

        .btn-login:hover{
            transform:translateY(-3px);
        }

        .back-home{
            text-align:center;
            margin-top:25px;
        }

        .back-home a{
            text-decoration:none;
            color:#667eea;
            font-weight:500;
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(30px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        @media(max-width:480px){
            .login-box{
                width:90%;
                padding:30px;
            }
        }
    </style>

</head>
<body>

<div class="login-box">

    <div class="logo">
        <i class="fas fa-user-shield"></i>
        <h2>Admin Login</h2>
        <p>FuzzyStress Analysis System</p>
    </div>

    <form action="process/login.php" method="POST">

        <div class="input-group">
            <label>Username</label>
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Masukkan Username" required>
            </div>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
        </div>

        <div class="remember">
            <label>
                <input type="checkbox"> Ingat Saya
            </label>

            <a href="#">Lupa Password?</a>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i>
            Login
        </button>

    </form>

    <div class="back-home">
        <a href="../index.php">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Beranda
        </a>
    </div>

</div>

</body>
</html>