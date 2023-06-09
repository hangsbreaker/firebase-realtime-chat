# firebase-realtime-chat
Simple chat app with Firebase realtime database

URL Reference

- Tutorial
  https://www.cometchat.com/tutorials/how-to-build-a-chat-app-with-firebase
- Doc query get by url json data
  https://firebase.google.com/docs/database/rest/retrieve-data

Firebase example rule
<pre>
{
"rules": {
"messages":{
".indexOn":["timestamp"]
},
".read": true,  
 ".write": true,  
 }
}
</pre>
