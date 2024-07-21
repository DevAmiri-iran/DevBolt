<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to DevBolt</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #f06, #004);
            color: #fff;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            max-width: 800px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }
        h1 {
            margin-bottom: 10px;
            font-size: 2.5em;
            line-height: 1.2;
        }
        p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        a:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .container {
                max-width: 90%;
                padding: 10px;
            }
            h1 {
                font-size: 2em;
            }
            p {
                font-size: 1em;
            }
            a {
                padding: 8px 16px;
            }
        }
        @media (max-width: 480px) {
            h1 {
                font-size: 1.5em;
            }
            p {
                font-size: 0.9em;
            }
            a {
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome to DevBolt 1</h1>
    <p>Building amazing applications with ease and efficiency.</p>
    <a href="https://github.com/DevAmiri-iran/DevBolt" target="_blank">Read the Documentation</a>
</div>
</body>
</html>
