 <style>
     * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
     }

     body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         min-height: 100vh;
         color: #333;
     }

     .container {
         max-width: 1400px;
         margin: 0 auto;
         padding: 20px;
     }

     .header {
         background: rgba(255, 255, 255, 0.95);
         backdrop-filter: blur(10px);
         border-radius: 15px;
         padding: 20px;
         margin-bottom: 20px;
         box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
     }

     .header-content {
         display: flex;
         align-items: center;
         /* sejajarkan vertikal */
         justify-content: center;
         /* judul tetap di tengah */
         position: relative;
     }

     .header-title {
         text-align: center;
     }

     .btn-back {
         position: absolute;
         left: 20px;
         /* tempel di sisi kiri */
         background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
         color: #fff;
         padding: 10px 18px;
         border-radius: 8px;
         text-decoration: none;
         font-weight: 600;
         font-size: 14px;
         box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
         transition: all 0.3s ease;
     }

     .btn-back:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
         background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
     }


     .header h1 {
         text-align: center;
         color: #4a5568;
         margin-bottom: 10px;
         font-size: 2.5em;
         font-weight: 700;
     }

     .header .subtitle {
         text-align: center;
         color: #718096;
         font-size: 1.1em;
     }



     .main-content {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 20px;
         margin-bottom: 20px;
     }

     .card {
         background: rgba(255, 255, 255, 0.95);
         backdrop-filter: blur(10px);
         border-radius: 15px;
         padding: 25px;
         box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
         border: 1px solid rgba(255, 255, 255, 0.2);
     }

     .card h2 {
         color: #4a5568;
         margin-bottom: 20px;
         font-size: 1.8em;
         font-weight: 600;
     }

     .form-group {
         margin-bottom: 15px;
     }

     .form-group label {
         display: block;
         margin-bottom: 5px;
         font-weight: 500;
         color: #4a5568;
     }

     .form-group input,
     .form-group select,
     .form-group textarea {
         width: 100%;
         padding: 12px;
         border: 2px solid #e2e8f0;
         border-radius: 8px;
         font-size: 16px;
         transition: all 0.3s ease;
     }

     .form-group input:focus,
     .form-group select:focus,
     .form-group textarea:focus {
         outline: none;
         border-color: #667eea;
         box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
     }

     .form-row {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 15px;
     }

     .btn {
         padding: 12px 24px;
         border: none;
         border-radius: 8px;
         font-size: 16px;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
         text-transform: uppercase;
         letter-spacing: 0.5px;
     }

     .btn-primary {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
     }

     .btn-primary:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
     }

     .btn-success {
         background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
         color: white;
     }

     .btn-success:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
     }

     .btn-danger {
         background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
         color: white;
     }

     .btn-danger:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(245, 101, 101, 0.3);
     }

     .btn-warning {
         background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
         color: white;
     }

     .btn-warning:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(237, 137, 54, 0.3);
     }

     .btn[disabled] {
         background: #a0aec0 !important;
         /* abu-abu */
         color: #e2e8f0 !important;
         cursor: not-allowed !important;
         box-shadow: none !important;
         transform: none !important;
     }


     .services-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 15px;
         margin-bottom: 20px;
     }

     .service-card {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
         padding: 20px;
         border-radius: 12px;
         text-align: center;
         cursor: pointer;
         transition: all 0.3s ease;
         border: none;
     }

     .service-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
     }

     .service-card h3 {
         font-size: 1.2em;
         margin-bottom: 10px;
     }

     .service-card .price {
         font-size: 1.5em;
         font-weight: 700;
     }

     .cart-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px;
     }

     .cart-table th,
     .cart-table td {
         padding: 12px;
         text-align: left;
         border-bottom: 1px solid #e2e8f0;
     }

     .cart-table th {
         background: #f7fafc;
         font-weight: 600;
         color: #4a5568;
     }

     .cart-table tr:hover {
         background: #f7fafc;
     }

     .total-section {
         background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
         color: white;
         padding: 20px;
         border-radius: 12px;
         margin-top: 20px;
     }

     .total-section h3 {
         font-size: 1.5em;
         margin-bottom: 10px;
     }

     .total-amount {
         font-size: 2.5em;
         font-weight: 700;
     }

     .payment-summary {
         margin: 15px 0;
         font-size: 1.1em;
     }

     .payment-summary .row {
         display: flex;
         justify-content: space-between;
         margin: 4px 0;
     }

     .payment-summary .row.total {
         border-top: 2px solid rgba(255, 255, 255, 0.6);
         padding-top: 8px;
         font-size: 1.3em;
     }


     .status-badge {
         padding: 6px 12px;
         border-radius: 20px;
         font-size: 0.9em;
         font-weight: 500;
         text-transform: uppercase;
         letter-spacing: 0.5px;
     }

     .status-pending {
         background: #fed7d7;
         color: #c53030;
     }

     .status-baru {
         background: #feebc8;
         color: #dd6b20;
     }

     .status-ready {
         background: #c6f6d5;
         color: #2f855a;
     }

     .status-completed {
         background: #bee3f8;
         color: #2b6cb0;
     }

     .transaction-list {
         max-height: 400px;
         overflow-y: auto;
     }

     .transaction-item {
         background: #f7fafc;
         padding: 15px;
         border-radius: 8px;
         margin-bottom: 10px;
         border-left: 4px solid #667eea;
     }

     .transaction-item h4 {
         color: #4a5568;
         margin-bottom: 5px;
     }

     .transaction-item p {
         color: #718096;
         margin-bottom: 5px;
     }

     .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         background: rgba(0, 0, 0, 0.5);
         backdrop-filter: blur(5px);
     }

     .modal-content {
         background: white;
         margin: 5% auto;
         padding: 30px;
         border-radius: 15px;
         width: 90%;
         max-width: 600px;
         max-height: 80vh;
         overflow-y: auto;
     }

     .close {
         color: #aaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
         cursor: pointer;
         line-height: 1;
     }

     .close:hover {
         color: #000;
     }

     .stats-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 15px;
         margin-bottom: 20px;
     }

     .stat-card {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
         padding: 20px;
         border-radius: 12px;
         text-align: center;
     }

     .stat-card h3 {
         font-size: 2em;
         margin-bottom: 10px;
     }

     .stat-card p {
         font-size: 1.1em;
         opacity: 0.9;
     }

     @media (max-width: 768px) {
         .main-content {
             grid-template-columns: 1fr;
         }

         .form-row {
             grid-template-columns: 1fr;
         }

         .header h1 {
             font-size: 2em;
         }

         .services-grid {
             grid-template-columns: 1fr;
         }
     }

     .receipt {
         background: white;
         padding: 20px;
         border-radius: 8px;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
         font-family: 'Courier New', monospace;
     }

     .receipt-header {
         text-align: center;
         border-bottom: 2px solid #333;
         padding-bottom: 10px;
         margin-bottom: 20px;
     }

     .receipt-item {
         display: flex;
         justify-content: space-between;
         margin-bottom: 5px;
     }

     .receipt-total {
         border-top: 2px solid #333;
         padding-top: 10px;
         margin-top: 10px;
         font-weight: bold;
     }
 </style>
