1) clone repo https://github.com/oznest/drumncode.git
2) in project folder run: make build
3) Than make test to run tests
4) open http://localhost:8080/api/doc for api documentation

For checking api do next steps:
1) Login with method POST /api/login 
{
"email": "user_1@example.com",
"password": "password"
}
2) Create task with method POST /api/task
3) Get id of your last task with method GET /api/tasks/search
4) Update task with method PATCH /api/tasks/{id}/status
5) Or delete task with method DELETE /api/tasks/{id}

6) For searching tasks use method GET /api/tasks/search it works with elasticsearch full text search.
