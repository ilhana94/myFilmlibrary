<?php
// backend/middleware/ValidationMiddleware.php

class ValidationMiddleware {
    
    public static function validate($rules) {
        return function() use ($rules) {
            $request = Flight::request();
            $data = [];
            
            // Handle JSON request
            if ($request->type == 'application/json') {
                $data = json_decode($request->getBody(), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Flight::json([
                        'success' => false,
                        'error' => 'Invalid JSON format'
                    ], 400);
                    return false;
                }
            } else {
                // Handle form data
                $data = $request->data->getData();
            }
            
            $errors = [];
            
            foreach ($rules as $field => $fieldRules) {
                $value = $data[$field] ?? null;
                $rulesArray = explode('|', $fieldRules);
                
                foreach ($rulesArray as $rule) {
                    // Required rule
                    if ($rule === 'required' && (empty($value) && $value !== '0')) {
                        $errors[$field][] = ucfirst($field) . ' is required';
                    }
                    
                    // Email validation
                    if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = 'Invalid email format';
                    }
                    
                    // Minimum length
                    if (strpos($rule, 'min:') === 0) {
                        $min = (int) explode(':', $rule)[1];
                        if (strlen($value) < $min) {
                            $errors[$field][] = ucfirst($field) . ' must be at least ' . $min . ' characters';
                        }
                    }
                    
                    // Password confirmation
                    if ($rule === 'confirmed') {
                        $confirmationField = $field . '_confirmation';
                        if (isset($data[$confirmationField]) && $value !== $data[$confirmationField]) {
                            $errors[$field][] = ucfirst($field) . ' confirmation does not match';
                        }
                    }
                }
            }
            
            if (!empty($errors)) {
                Flight::json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $errors
                ], 400);
                return false;
            }
            
            // Store validated data
            Flight::set('validated_data', $data);
            return true;
        };
    }
}