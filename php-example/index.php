<?php
function curl($url, $method, $json = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($json)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

$db = "https://[firebase_app_url]xxxxxxxxxxxxxxxxxxxxxxxx/messages";

if (isset($_POST["msg"])) {
    if ($_POST['msg'] == '') {
        $res = curl($db . '.json?orderBy="timestamp"&limitToLast=2000', 'GET');
    } else {
        $res = curl($db . '.json?orderBy="timestamp"&startAt=' . $_POST["msg"], 'GET');
    }
    echo $res;
    exit;
}

if (isset($_POST["username"], $_POST["message"])) {
    $t = intval(microtime(true) * 1000);
    $data = json_encode(
        array(
            "timestamp" => $t,
            "message" => $_POST["message"],
            "username" => $_POST["username"]
        )
    );
    $res = curl($db . '/' . $t . '.json', "PUT", $data);
    print_r($res);
    exit;
}

// $res = curl($db . '.json?orderBy="$key"&limitToLast=2000', 'GET');
// $res = curl($db . '.json?orderBy="timestamp"&startAt=1686281447618', 'GET');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Firebase RealTime Chat</title>
    <style>
        body {
            background-color: #f3f2f3;
            margin: 0;
            padding: 0;
        }

        header {
            text-align: center;
        }

        #messages {
            padding-bottom: 30%;
        }

        li {
            list-style-type: none;
            margin-bottom: 10px;
            color: white;
            width: 100%;
        }

        li .message {
            padding: 8px;
            border-radius: 6px;
            width: 50%;
        }

        li .message span {
            font-weight: bolder;
            display: block;
            text-align: left;
        }

        #chat {
            position: relative;
            width: 100%;
            margin: auto;
            left: -28px;
        }

        #message-form {
            text-align: center;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #b7b5b9;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .input {
            width: 70%;
            height: 30px;
            border: none;
            border-radius: 3px;
            outline: none;
            padding-left: 6px;
            padding-right: 6px;
        }

        .btn {
            width: 25%;
            height: 32px;
            border: none;
            border-radius: 3px;
        }

        .receive {
            color: #55e9c9;
            background-color: #ffffff;
        }

        .sent {
            text-align: right;
            margin-left: 50%;
            background-color: #ffffff;
        }

        .sent .name {
            color: #297cca;
        }

        .text {
            color: #000000;
            font-weight: normal !important;
        }
    </style>
</head>

<body>
    <header>
        <h2>Firebase RealTime Chat</h2>
    </header>

    <div id="chat">
        <!-- messages will display here -->
        <ul id="messages"></ul>

        <!-- form to send message -->
        <form id="message-form">
            <input id="message-input" class="input" type="text" />
            <button id="send-btn" class="btn" type="button">Send</button>
        </form>
    </div>
    <!-- scripts -->
    <script src="js/jquery.1.12.4.min.js"></script>
    <script>
        let dtm = "";
        let messages = {};
        const username = prompt("Please Tell Us Your Name");
        $("#send-btn").click(() => {
            let message = $("#message-input").val();
            let timestamp = Date.now();
            dtm = timestamp;

            $("#message-input").val("");
            $.ajax({
                type: "POST",
                url: window.location.pathname,
                data: {
                    timestamp: timestamp,
                    username: username,
                    message: message
                },
                success: function (data) {
                    console.log(data);
                    // getmsg(timestamp);
                }
            });
        });


        const getmsg = (data = "") => {
            $.ajax({
                type: "POST",
                url: window.location.pathname,
                data: {
                    msg: data
                },
                success: function (data) {
                    let d = JSON.parse(data);
                    for (i in d) {
                        if (!messages.hasOwnProperty(i)) {
                            messages[i] = d[i];
                            loadmsg(d[i]);
                        }
                    }
                }
            });
        }

        const loadmsg = (data) => {
            const message = `<li><div class="message ${username === data.username ? "sent" : "receive"
                }"><span class="name">${data.username}: </span><span class="text">${data.message}</span></div></li>`;
            // append the message on the page
            document.getElementById("messages").innerHTML += message;
        }

        setInterval(() => {
            getmsg(dtm);
        }, 500);
    </script>
</body>

</html>