<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0081A7 0%, #00AFB9 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #0081A7;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0081A7;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .total-box {
            background: linear-gradient(135deg, #0081A7 0%, #00AFB9 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .total-box .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .total-box .amount {
            font-size: 32px;
            font-weight: bold;
        }
        .reminder-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .reminder-box h4 {
            margin-top: 0;
            color: #856404;
            font-size: 16px;
        }
        .reminder-box ul {
            margin: 10px 0;
            padding-left: 20px;
            color: #856404;
        }
        .reminder-box li {
            margin: 5px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer strong {
            color: #0081A7;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🎉 Booking Confirmed!</h1>
            <p>Reference: {{ $booking->payment->reference_number }}</p>
        </div>
        
        <div class='content'>
            <p class='greeting'>Dear {{ $booking->client->name }},</p>
            
            <p>Thank you for choosing {{ $companyName }}! Your payment has been successfully processed and your booking is confirmed.</p>
            
            <div class='info-box'>
                <h3>📋 Booking Details</h3>
                <div class='info-row'>
                    <span class='info-label'>Vehicle:</span>
                    <span class='info-value'>{{ $vehicleName }}</span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>Type:</span>
                    <span class='info-value'>{{ $booking->vehicle->body_type }}</span>
                </div>
                @if($booking->vehicle->license_plate)
                <div class='info-row'>
                    <span class='info-label'>Plate Number:</span>
                    <span class='info-value'>{{ $booking->vehicle->license_plate }}</span>
                </div>
                @endif
                <div class='info-row'>
                    <span class='info-label'>Pickup Date:</span>
                    <span class='info-value'>{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>Return Date:</span>
                    <span class='info-value'>{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>Duration:</span>
                    <span class='info-value'>{{ $rentalDays }} day{{ $rentalDays > 1 ? 's' : '' }}</span>
                </div>
            </div>
            
            <div class='total-box'>
                <div class='label'>Total Amount Paid</div>
                <div class='amount'>₱{{ number_format($booking->payment->amount, 2) }}</div>
                <div style='margin-top: 10px; font-size: 14px; opacity: 0.9;'>
                    Payment Method: {{ strtoupper(str_replace('_', ' ', $booking->payment->payment_method)) }}
                </div>
            </div>
            
            <div class='info-box'>
                <h3>🚗 Operator Information</h3>
                <div class='info-row'>
                    <span class='info-label'>Name:</span>
                    <span class='info-value'>{{ $booking->operator->name }}</span>
                </div>
                @if($booking->operator->email)
                <div class='info-row'>
                    <span class='info-label'>Email:</span>
                    <span class='info-value'>{{ $booking->operator->email }}</span>
                </div>
                @endif
                @if($booking->operator->phone)
                <div class='info-row'>
                    <span class='info-label'>Phone:</span>
                    <span class='info-value'>{{ $booking->operator->phone }}</span>
                </div>
                @endif
            </div>
            
            <div class='reminder-box'>
                <h4>⚠️ Important Reminders</h4>
                <ul>
                    <li>Arrive 15 minutes early for vehicle inspection</li>
                    <li>Bring valid driver's license and government-issued ID</li>
                    <li>Security deposit will be required at pickup</li>
                    <li>Return vehicle with the same fuel level</li>
                    <li>No smoking inside the vehicle</li>
                    <li>Contact operator for any changes or concerns</li>
                </ul>
            </div>
            
            @if($booking->notes)
            <div class='info-box'>
                <h3>📝 Additional Notes</h3>
                <p style='margin: 0; white-space: pre-wrap;'>{{ $booking->notes }}</p>
            </div>
            @endif
            
            <p>If you have any questions or need assistance, please don't hesitate to contact us at {{ $companyEmail }} or call {{ $companyPhone }}.</p>
            
            <p style='margin-top: 30px;'>
                Best regards,<br>
                <strong>{{ $companyName }} Team</strong>
            </p>
        </div>
        
        <div class='footer'>
            <p><strong>{{ $companyName }}</strong></p>
            <p>{{ $companyAddress }}</p>
            <p>{{ $companyPhone }} | {{ $companyEmail }}</p>
            <p style='margin-top: 15px; font-size: 11px;'>
                This is an automated email. Please do not reply directly to this message.
            </p>
            <p style='font-size: 10px; margin-top: 10px;'>
                Generated on {{ now()->format('F d, Y \a\t h:i A') }}
            </p>
        </div>
    </div>
</body>
</html>