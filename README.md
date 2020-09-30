## Endpoints

All endpoints give back error, if the header parameter 'userID' is missing or there is no user in the database according to it. Otherwise the following endpoints should give back valid JSON responses.

```
GET
/products
```
```
GET
/products?page=2
```
```
GET
/products?code=000004
```
This endpoint gives back the paginated (as shown above) list of products with their average rating values. You can append a GET variable named 'code' to filter the products according to their code (as shown above).

```
GET
/product/4
```
This endpoint gives back a single product according to it's id with it's ratings.

```
POST
/rate/4
```
```
{
    "value": 5 // required, must be numeric value, must be between 1 & 10
}
```
This endpoint creates a rating of the product with the given id, related to the product and the user as well. A single user is only able to rate a product once. The endpoint accepts the data in JSON (as shown above). There are validation rules set up against the data sent from this endpoint.

```
PUT
/product/4
```
```
{
    "code": "000003", // must be 6 digits long, alphanumeric 
    "name": "Product 3", // required
    "price": "55.55", // required, must be numeric value, must be greater than 0
    "description": "Description 3"
}
```
This endpoint updates the a product with the given data, if the user has already rated it, as a product can only be related to a user through a rating. Otherwise we send a response with status code 403 (Forbidden). The endpoint accepts the data in JSON (as shown above). There are validation rules set up against the data sent from this endpoint.
