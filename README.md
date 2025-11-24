# Open Study

## Description

This project is a learing platform made for students to make learning interactive and fun. Focused on its community to create learning lists for everyone to use. Heavily inspired by the platform [Quizlet](https://quizlet.com).

## Backend

### API (PHP)

- Slim

- Model / Controller

## Frontend

### Website (CSS, JS, HTML)

- Bootstrap

- Mobile compatible

## Models

### Set

Represents a list of terms made by a user available for everyone to use

| Proprieties | Description                                                    |
| ----------- | -------------------------------------------------------------- |
| id          | Unique identifier, given by the database when a set is created |
| name        | Name given by the user when they made the set                  |
| terms       | List of all the terms of the set                               |
| userId      | Identifier of the user who made the set                        |

### Term

Represents what the users will be studying

| Proprieties | Description                                                     |
| ----------- | --------------------------------------------------------------- |
| id          | Unique identifier, given by the database when a term is created |
| original    | Clue given by the user to find the definition                   |
| definition  | The meaning of the original                                     |
| setId       | Identifier of the set in which the term is stored               |

### User

Represents an account of a student

| Proprieties | Description                                                                         |
| ----------- | ----------------------------------------------------------------------------------- |
| id          | Unique identifier, given by the database when a student account is created          |
| username    | Unique, name the user wants to be known as                                          |
| password    | Hashed version of the password given by the student when they created their account |

### Sepcifications

- A user can login and sign up into the website with their username and password

- A user can create a set

- A user can logount

- A user can change their username

- A user can study any set

- A user can create a set

- A user can update their own sets

- A user can delete their own sets
