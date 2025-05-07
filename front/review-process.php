<style>
        
        .reviews-container {
            max-width: 2000px;
            margin: 1rem auto;
            padding: 0 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .section-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .section-title h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: #666;
            font-size: 1rem;
        }

        /* Two-column layout */
        .reviews-layout {
            display: flex;
            gap: 1.5rem;
        }

        .review-form-column {
            flex: 1;
            min-width: 350px;
        }

        .reviews-column {
            flex: 1;
            min-width: 350px;
        }

        /* Review Form */
        .add-review {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }

        .add-review h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 0.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 0.5rem 0 1rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 1.8rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffc107;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .submit-btn {
            background: black;
            color: white;
            border: none;
            padding: 0.8rem;
            font-size: 0.95rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: 500;
            margin-top: 0.5rem;
            width: 100%;
        }

        .submit-btn:hover {
            background: white;

        }

        /* Scrollable Reviews */
        .review-cards {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.8rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Custom scrollbar */
        .review-cards::-webkit-scrollbar {
            width: 5px;
        }

        .review-cards::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .review-cards::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .review-cards::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .review-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            padding: 1.2rem;
            transition: transform 0.2s ease;
        }

        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .review-stars {
            color: #ffc107;
            font-size: 1.1rem;
            margin-bottom: 0.6rem;
        }

        .review-text {
            color: #444;
            line-height: 1.5;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
        }

        .reviewer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.8rem;
            border: 1px solid #f0f0f0;
        }

        .reviewer-details h4 {
            margin: 0;
            color: #333;
            font-size: 0.95rem;
        }

        .reviewer-details p {
            margin: 0.1rem 0 0;
            color: #777;
            font-size: 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 800px) {
            .reviews-layout {
                flex-direction: column;
            }

            .review-form-column,
            .reviews-column {
                width: 100%;
            }

            .review-cards {
                max-height: none;
                overflow-y: visible;
            }

            .add-review {
                position: static;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .section-title h2 {
                font-size: 1.5rem;
            }

            .add-review {
                padding: 1.2rem;
            }

            .add-review h3 {
                font-size: 1.1rem;
            }

            .star-rating label {
                font-size: 1.5rem;
            }

            textarea.form-control {
                min-height: 80px;
            }
        }
    
    </style>