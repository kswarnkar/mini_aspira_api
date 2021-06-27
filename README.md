# About Mini Aspire API

Mini Aspire is base application programing interface developed for loan approver and loan repayment and it has user registration, login functionality. We are using JWT library for user authentication. we are using carbon library for term calculation (Specifically weeks time). once user logged in, will receive bearer token and it's valid for 1 hour. user_profile api show user data.

The loan model file has emi_calculator function, which calculates on weekly bases emi. User can only submit loan amount and loan term, rest of the details application it self calculated and will show details.

## Installation

#### Application installation step by step.

1. Download the Mini_Aspire_Api, open terminal and copy given code:-

```
git clone https://github.com/kswarnkar/mini-aspire-api.git
```

2. go inside download repo

```
cd mini_aspire_api
```

3. Installs the project dependencies from the composer.lock file. After that run dumps autoloader files.

```
composer update
composer dump-autoload
```

4. Copy environment file and generate APP_KEY and JWT_SECRET Key

```
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

6. set database credential on .env file

7. Start Laravel development server

```
php artisan serve
```

## Create jwt middleware

- I have created a middleware which protects and verified API routes. To use this middleware register this into Kernel. "app\Http\Kernel.php"

```
protected $routeMiddleware = [
        'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
        'jwt.auth' => 'Tymon\JWTAuth\Middleware\GetUserFromToken',
        'jwt.refresh' => 'Tymon\JWTAuth\Middleware\RefreshToken',
];
```

- This middleware is verifying that the user is authenticated by checking the token sent in the request’s header and It will create a new middleware file in your Middleware directory. In case of user is not authenticated, middleware will throw UnauthorizedHttpException error.

## Create API Routes

- Created rest API routes for restful_authentication_API with the help of JwtMiddleware, which internally uses jwt-verify route.

## Models

- Mini Aspire Api has three models:-

1. User model has many relationship with loans and repayments model. User model protected fillable fields are - name, email, password.
2. Loan model protected fillable fields are - user_id, amount, yearly_term, percentage, weekly_term, emi, status. The loan model has emi_calculator method (logic, function), which calculate on weekly basis emi. emi_calculator method is required three fields value, which is loan amount, rate of interest (percentage) and term duration.
3. Repayment model protected fillable fields are - user_id, total_paid_amount, emi, week_day, remaining_period.

## Controller Logics

- This api controller has three controllers:-

1. ApiController has four methods namely register, authenticate, logout, get_user.

- Register method **"POST Method"** :- registration route api is not authentication
  Required fields are user name, email, password, It validates given data. If user data is incorrect order, method return error message or user data is correct order, it will create new user and return success status.
- Authenticate method **"POST Method"** :- login route api is not authentication
  Required fields are user email & password and will validate user data.
  Then return success message with token or error message when user is not available.
  JWTAuth check the user credentials and JWTException show exception error.
- Logout method:- **"GET Method"**
  Required user token and validate
  JWTAuth invalidate the token or show JWTException message.
- Get User method:- **"GET Method"**
  Required user token and validate after that check JWTAuth authenticate user and send user data.

2. LoanController has two methods namely index (show status of user loan request) and create (user loan request) these methods is only for authorized user. I created construct method which authenticate JWTAuth parseToken user and declared user as a protected property in LoanController.

- Loan index:- **"GET Method"**
  It return four types of alert notification message for user:- eligibility for loan, loan approval is awaited, loan approved, next week loan emi date, based on user loan request.
- Loan create:- **"POST Method"**
  Required two fields amount, yearly_term and validate.
  loan percentage is manually declared in protected field at LoanController file.
  weekly_term is calculated by multiply 52 in the yearly_term field.
  EMI calculate by loan model -> emi_calculator method
  The field create and stored in user eloquent relationship model.
  The field create and stored by using user eloquent relationship model.
- For user Loan approval open terminal and run the command:-
  ```
  php artisan tinker
  App\Models\Loan::where('status','=',0)->first()->update(['status' => 1]);
  ```

3. RepaymentController has two methods index () and create () these methods is only for authorized user. I created construct method which authenticate JWTAuth parseToken user and declared user as a protected property in RepaymentController.

- Repayment index:- **"GET Method"**
  In this method we are returning notification message
  1.  we did not find any installment history!
  2.  when installment received, it will show details json formate.
- Repayment create:- **"POST Method"**
  It required a field emi and user eloquent relationship model stored other details by fetching user loan table fields.

# Feature Test

## AuthTest.php

- test_rester
- test_login
- test_logout
- test_get_user

## LoanTest.php

- test_loan_application_status
- test_loan_apply

## RepaymentTest.php

- test_repayment_status
- test_loan_repayment

# POSTMAN Collection, Request & Response

## User Registration

POST [http://127.0.0.1:8000/api/register](http://127.0.0.1:8000/api/register)
User registration api (without authentication).

### Request body

```JSON
{
  "name":"Jone Doe",
  "email":"jone@example.com",
  "password":"password"
}
```

- Name : your name (required)
- email : demo@xyz.com (required)
- password : password (minimum 8 character long, required)

### Response

```JSON
{
  "success": true,
  "message": "User created successfully",
  "data": {
      "name": "Jone Doe",
      "email": "jone@example.com",
      "updated_at": "2021-06-27T19:42:21.000000Z",
      "created_at": "2021-06-27T19:42:21.000000Z",
      "id": 7
  }
}
```

## User Login

POST [http://127.0.0.1:8000/api/login](http://127.0.0.1:8000/api/login)
Login with your registered test credentials.
successfully login will provide you a bearer token.

### Request

```JSON
{
  "email":"jone@example.com",
  "password":"password"
}
```

### Response

```JSON
{
    "success": true,
    "token": "example access token"
}
```

## User Profile

GET [http://127.0.0.1:8000/api/get_user](http://127.0.0.1:8000/api/get_user)

### Request

- To access user profile required bearer token.

```JSON
{
    "token": "example access token"
}
```

### Response

```JSON
{
    "user": {
        "id": 7,
        "name": "Jone Doe",
        "email": "jone@example.com",
        "email_verified_at": null,
        "created_at": "2021-06-27T19:42:21.000000Z",
        "updated_at": "2021-06-27T19:42:21.000000Z"
    }
}
```

## Check Eligibility for loan

GET [http://127.0.0.1:8000/api/loans](http://127.0.0.1:8000/api/loans)

### Request

- To access loans/index required bearer token.

```JSON
{
    "token": "example access token"
}
```

### Initial response

- once user registered he is able to apply for a loan.

```JSON
  {
    "message": "now you can apply for the Loan!",
    "details": "",
  }
```

### Second response

- second response after applied for a loan and wait for the approval
- Loan approval once open terminal and run php artisan tinker
  1.  $loan = App\Models\Loan::where('status','=',0)->first()->update(['status' => 1]);

```JSON
  {
    "message": "your loan approval is awaited",
    "details": [
        {
            "id": 1,
            "user_id": 1,
            "amount": 200000,
            "term": 4,
            "percentage": 6,
            "weekly_term": 208,
            "emi": 1082.09,
            "status": 0,
            "created_at": "2021-06-22T16:00:40.000000Z",
            "updated_at": "2021-06-22T16:00:40.000000Z"
        }
    ]
  }
```

### Third response

- once your loan approved, response updated.

```JSON
{
    "message": "congratulations your loan has been approved.",
    "next_installment_date": "your next week installment date is 2021-06-29 16:00:40",
    "details": [
        {
            "id": 1,
            "user_id": 1,
            "amount": 200000,
            "term": 4,
            "percentage": 6,
            "weekly_term": 208,
            "emi": 1082.09,
            "status": 1,
            "created_at": "2021-06-22T16:00:40.000000Z",
            "updated_at": "2021-06-22T16:00:40.000000Z"
        }
    ]
}
```

### Fourth response

- Successfully paid first installment, updated response

```JSON
{
    "message": "your next week installment date is 2021-07-06 16:00:40",
    "next_installment_date": "2021-07-06 16:00:40",
    "details": {
        "id": 2,
        "user_id": 1,
        "total_paid_amount": 2164.18,
        "emi": 1082.09,
        "next_date": "2021-07-06 16:00:40",
        "remaining_period": 206,
        "created_at": "2021-06-22T16:26:41.000000Z",
        "updated_at": "2021-06-22T16:26:41.000000Z"
    }
}
```

## Apply for a loan

POST [http://127.0.0.1:8000/api/create](http://127.0.0.1:8000/api/create)

### Request

- To access with header bearer token.

```JSON
  {
    "amount": "200000",
    "term": "4",
  }
```

- term field should always declare in years and dynamically converted into weeks.
  if you required
  - 9 month => term field => 0.75 year.
  - 6 month => term field => 0.50 year.
  - 3 month => term field => 0.25 year.

### Response

```JSON
{
    "status": "your loan application is successfully submitted",
    "loan": {
        "amount": "200000",
        "term": "4",
        "percentage": 6,
        "weekly_term": 208,
        "emi": 1082.0860109122548,
        "user_id": 1,
        "updated_at": "2021-06-22T16:00:40.000000Z",
        "created_at": "2021-06-22T16:00:40.000000Z",
        "id": 1
    }
}
```

## Check repayments history

GET [http://127.0.0.1:8000/api/repayments](http://127.0.0.1:8000/api/repayments)

### Request

- Required Bearer token

```JSON
{
    "token": "example access token"
}
```

### Initial

```JSON
  {
    "message": "we did not find any installment history!"
  }
```

### Second response

- once you start paying installments.

```JSON
{
  "status" : true,
  "data":
    {
        "id": 1,
        "user_id": 1,
        "total_paid_amount": 1082.09,
        "emi": 1082.09,
        "next_date": "2021-06-29 16:00:40",
        "remaining_period": 207,
        "created_at": "2021-06-22T16:23:46.000000Z",
        "updated_at": "2021-06-22T16:23:46.000000Z"
    }
}
```

## Repayment Api

POST [http://127.0.0.1:8000/api/repayCreate](http://127.0.0.1:8000/api/repayCreate)

### Request body

- Required bearer token

```JSON
  {
    "emi": "1082.09"
  }
```

### Response

```JSON
{
  "status": "your installment paid successfully.",
  "repayment": {
      "total_paid_amount": 2164.18,
      "emi": "1082.09",
      "next_date": "2021-07-06T16:00:40.000000Z",
      "remaining_period": 206,
      "user_id": 1,
      "updated_at": "2021-06-22T16:26:41.000000Z",
      "created_at": "2021-06-22T16:26:41.000000Z",
      "id": 2
  }
}
```

## Logout user

GET [http://127.0.0.1:8000/api/logout](http://127.0.0.1:8000/api/logout)

### Request

- required bearer token

```JSON
{
    "token": "example access token"
}
```

### Response

```JSON
{
    "success": true,
    "message": "User has been logged out"
}
```
