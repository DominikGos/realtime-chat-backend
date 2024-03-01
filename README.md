# Description 

This is the "rest API" for the chat project. With this application you can chat with other people. Manage your own profile. Sending files to other users.
When a user sends a request to store a message, the API dispatch a "Message Sent" event. These events are broadcast, 
so the front-end application has the ability to listen to these events and allows users to conduct conversations in real time.

Link to the front-end application https://github.com/DominikGos/realtime-chat-frontend
# Technologies: 

 * Laravel 10
 * Php 8 
 * Pusher 

# Example endpoints
[screen-recorder-tue-nov-21-2023-22-11-00.webm](https://github.com/DominikGos/realtime-chat-backend/assets/85825266/862d26bf-6a1a-40a6-a15d-5f5d6b319de2)

# Installation
The application includes a docker-compose file, so running this project only requires an installed DOCKER and a configured .env file. If you are using Windows, you also need to install WSL2 as the SAIL package requires it. More information about wsl and sail package can be found here https://laravel.com/docs/10.x#sail-on-windows. If you have installed docker on your computer, in the root of the project run.

```bash
    ./vendor/bin/sail up -d
```

# Testing 
The app includes some basic tests, so if you installed the app with docker, execute this command to run the tests.

```bash
    ./vendor/bin/sail artisan test
```
