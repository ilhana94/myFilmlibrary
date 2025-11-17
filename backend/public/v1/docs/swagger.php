<?php
$openapi = [
    'openapi' => '3.0.0',
    'info' => [
        'title' => 'My Film Library API',
        'version' => '1.0.0',
        'description' => 'Complete API for film library management'
    ],
    'servers' => [
        ['url' => 'http://localhost/my-film-library/backend']
    ],
    'paths' => [
        // CATEGORIES
        '/categories' => [
            'get' => [
                'summary' => 'Get all categories',
                'tags' => ['Categories'],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'name' => ['type' => 'string', 'example' => 'Action']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'post' => [
                'summary' => 'Create a new category',
                'tags' => ['Categories'],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string', 'example' => 'Comedy']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Category created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 5],
                                        'name' => ['type' => 'string', 'example' => 'Comedy']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        '/categories/{id}' => [
            'get' => [
                'summary' => 'Get category by ID',
                'tags' => ['Categories'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 1],
                                        'name' => ['type' => 'string', 'example' => 'Action']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => ['description' => 'Category not found']
                ]
            ],
            'put' => [
                'summary' => 'Update category by ID',
                'tags' => ['Categories'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string', 'example' => 'Updated Action']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Category updated'],
                    '404' => ['description' => 'Category not found']
                ]
            ],
            'delete' => [
                'summary' => 'Delete category by ID',
                'tags' => ['Categories'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Category deleted'],
                    '404' => ['description' => 'Category not found']
                ]
            ]
        ],

        // MOVIES
        '/movies' => [
            'get' => [
                'summary' => 'Get all movies',
                'tags' => ['Movies'],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'title' => ['type' => 'string', 'example' => 'Inception'],
                                            'description' => ['type' => 'string', 'example' => 'A mind-bending thriller'],
                                            'release_year' => ['type' => 'integer', 'example' => 2010],
                                            'poster_url' => ['type' => 'string', 'example' => 'https://example.com/poster.jpg'],
                                            'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'post' => [
                'summary' => 'Create a new movie',
                'tags' => ['Movies'],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => ['type' => 'string', 'example' => 'The Matrix'],
                                    'description' => ['type' => 'string', 'example' => 'Sci-fi action movie'],
                                    'release_year' => ['type' => 'integer', 'example' => 1999],
                                    'poster_url' => ['type' => 'string', 'example' => 'https://example.com/matrix.jpg']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Movie created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 10],
                                        'title' => ['type' => 'string', 'example' => 'The Matrix'],
                                        'description' => ['type' => 'string', 'example' => 'Sci-fi action movie'],
                                        'release_year' => ['type' => 'integer', 'example' => 1999],
                                        'poster_url' => ['type' => 'string', 'example' => 'https://example.com/matrix.jpg'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        '/movies/{id}' => [
            'get' => [
                'summary' => 'Get movie by ID',
                'tags' => ['Movies'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 1],
                                        'title' => ['type' => 'string', 'example' => 'Inception'],
                                        'description' => ['type' => 'string', 'example' => 'A mind-bending thriller'],
                                        'release_year' => ['type' => 'integer', 'example' => 2010],
                                        'poster_url' => ['type' => 'string', 'example' => 'https://example.com/poster.jpg'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => ['description' => 'Movie not found']
                ]
            ],
            'put' => [
                'summary' => 'Update movie by ID',
                'tags' => ['Movies'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => ['type' => 'string', 'example' => 'Inception Updated'],
                                    'description' => ['type' => 'string', 'example' => 'Updated description'],
                                    'release_year' => ['type' => 'integer', 'example' => 2010],
                                    'poster_url' => ['type' => 'string', 'example' => 'https://example.com/new-poster.jpg']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Movie updated'],
                    '404' => ['description' => 'Movie not found']
                ]
            ],
            'delete' => [
                'summary' => 'Delete movie by ID',
                'tags' => ['Movies'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Movie deleted'],
                    '404' => ['description' => 'Movie not found']
                ]
            ]
        ],

        // MOVIE CATEGORIES (veza many-to-many)
        '/movie_categories' => [
            'post' => [
                'summary' => 'Add category to movie',
                'tags' => ['Movie Categories'],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'movie_id' => ['type' => 'integer', 'example' => 1],
                                    'category_id' => ['type' => 'integer', 'example' => 1]
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Category added to movie',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 1],
                                        'movie_id' => ['type' => 'integer', 'example' => 1],
                                        'category_id' => ['type' => 'integer', 'example' => 1]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        '/movie_categories/{id}' => [
            'delete' => [
                'summary' => 'Remove category from movie',
                'tags' => ['Movie Categories'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Category removed from movie'],
                    '404' => ['description' => 'Movie category not found']
                ]
            ]
        ],

        // REVIEWS
        '/reviews' => [
            'get' => [
                'summary' => 'Get all reviews',
                'tags' => ['Reviews'],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'movie_id' => ['type' => 'integer', 'example' => 1],
                                            'user_id' => ['type' => 'integer', 'example' => 1],
                                            'rating' => ['type' => 'integer', 'example' => 5],
                                            'comment' => ['type' => 'string', 'example' => 'Great movie!'],
                                            'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'post' => [
                'summary' => 'Create a new review',
                'tags' => ['Reviews'],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'movie_id' => ['type' => 'integer', 'example' => 1],
                                    'user_id' => ['type' => 'integer', 'example' => 1],
                                    'rating' => ['type' => 'integer', 'example' => 5],
                                    'comment' => ['type' => 'string', 'example' => 'Amazing film!']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Review created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 5],
                                        'movie_id' => ['type' => 'integer', 'example' => 1],
                                        'user_id' => ['type' => 'integer', 'example' => 1],
                                        'rating' => ['type' => 'integer', 'example' => 5],
                                        'comment' => ['type' => 'string', 'example' => 'Amazing film!'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        '/reviews/{id}' => [
            'get' => [
                'summary' => 'Get review by ID',
                'tags' => ['Reviews'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 1],
                                        'movie_id' => ['type' => 'integer', 'example' => 1],
                                        'user_id' => ['type' => 'integer', 'example' => 1],
                                        'rating' => ['type' => 'integer', 'example' => 5],
                                        'comment' => ['type' => 'string', 'example' => 'Great movie!'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => ['description' => 'Review not found']
                ]
            ],
            'put' => [
                'summary' => 'Update review by ID',
                'tags' => ['Reviews'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'rating' => ['type' => 'integer', 'example' => 4],
                                    'comment' => ['type' => 'string', 'example' => 'Updated review text']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Review updated'],
                    '404' => ['description' => 'Review not found']
                ]
            ],
            'delete' => [
                'summary' => 'Delete review by ID',
                'tags' => ['Reviews'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Review deleted'],
                    '404' => ['description' => 'Review not found']
                ]
            ]
        ],

        // USERS
        '/users' => [
            'get' => [
                'summary' => 'Get all users',
                'tags' => ['Users'],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'username' => ['type' => 'string', 'example' => 'john_doe'],
                                            'email' => ['type' => 'string', 'example' => 'john@example.com'],
                                            'role' => ['type' => 'string', 'example' => 'user'],
                                            'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'post' => [
                'summary' => 'Create a new user',
                'tags' => ['Users'],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string', 'example' => 'jane_smith'],
                                    'email' => ['type' => 'string', 'example' => 'jane@example.com'],
                                    'password' => ['type' => 'string', 'example' => 'securepassword123'],
                                    'role' => ['type' => 'string', 'example' => 'user']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'User created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 2],
                                        'username' => ['type' => 'string', 'example' => 'jane_smith'],
                                        'email' => ['type' => 'string', 'example' => 'jane@example.com'],
                                        'role' => ['type' => 'string', 'example' => 'user'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        '/users/{id}' => [
            'get' => [
                'summary' => 'Get user by ID',
                'tags' => ['Users'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer', 'example' => 1],
                                        'username' => ['type' => 'string', 'example' => 'john_doe'],
                                        'email' => ['type' => 'string', 'example' => 'john@example.com'],
                                        'role' => ['type' => 'string', 'example' => 'user'],
                                        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => ['description' => 'User not found']
                ]
            ],
            'put' => [
                'summary' => 'Update user by ID',
                'tags' => ['Users'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string', 'example' => 'updated_username'],
                                    'email' => ['type' => 'string', 'example' => 'updated@example.com']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'User updated'],
                    '404' => ['description' => 'User not found']
                ]
            ],
            'delete' => [
                'summary' => 'Delete user by ID',
                'tags' => ['Users'],
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'User deleted'],
                    '404' => ['description' => 'User not found']
                ]
            ]
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($openapi);
?>