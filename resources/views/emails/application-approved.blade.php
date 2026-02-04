<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #40916c;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px 20px;
        }

        .success-badge {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #40916c;
            padding: 15px;
            margin: 20px 0;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            color: #40916c;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }

        .btn {
            display: inline-block;
            background-color: #40916c;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #2d6e47;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Application Approved!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-badge">
                Your
                {{ $applicationType === 'seedling' || $applicationType === 'Supplies Request'
                    ? 'Supplies & Garden Tools Request'
                    : ($applicationType === 'rsbsa'
                        ? 'RSBSA Registration'
                        : ($applicationType === 'fishr'
                            ? 'FishR Registration'
                            : ($applicationType === 'boatr'
                                ? 'BoatR Registration'
                                : 'Application'))) }}
                has been approved!
            </div>

            <p>Dear {{ $application->first_name }} {{ $application->last_name }},</p>

            <p>Congratulations! We are pleased to inform you that your application has been <strong>approved</strong> by
                our office.</p>

            <!-- Application Details -->
            <div class="info-box">
                <h3>Application Details</h3>
                @if ($applicationType === 'seedling')
                    <div class="info-row">
                        <span class="info-label">Request Number:</span>
                        <span>{{ $application->request_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Items Requested:</span>
                        <span>{{ $application->seedling_type }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Quantity:</span>
                        <span>{{ $application->total_quantity }}</span>
                    </div>
                @elseif($applicationType === 'rsbsa')
                    <div class="info-row">
                        <span class="info-label">Application Number:</span>
                        <span>{{ $application->application_number }}</span>
                    </div>
                    @if ($application->rsbsa_reference_number)
                        <div class="info-row">
                            <span class="info-label">RSBSA Reference Number:</span>
                            <span>{{ $application->rsbsa_reference_number }}</span>
                        </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Main Livelihood:</span>
                        <span>{{ $application->main_livelihood }}</span>
                    </div>
                @elseif($applicationType === 'fishr')
                    <div class="info-row">
                        <span class="info-label">Registration Number:</span>
                        <span>{{ $application->registration_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Main Livelihood:</span>
                        <span>{{ $application->livelihood_description }}</span>
                    </div>
                @elseif($applicationType === 'boatr')
                    <div class="info-row">
                        <span class="info-label">Application Number:</span>
                        <span>{{ $application->application_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Vessel Name:</span>
                        <span>{{ $application->vessel_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">FishR Number:</span>
                        <span>{{ $application->fishr_number }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Barangay:</span>
                    <span>{{ $application->barangay }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date Applied:</span>
                    <span>{{ $application->created_at->format('F d, Y g:i A') }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="info-box">
                <h3>Next Steps</h3>
                @if ($applicationType === 'seedling' || $applicationType === 'Supplies Request')
                    @if (isset($application->pickup_date))
                        <p><strong>📅 Pickup Window:</strong></p>
                        <p style="font-size: 16px; color: #40916c; font-weight: bold;">
                            Within 30 days from approval<br>
                            Deadline: {{ $application->pickup_date->format('F d, Y') }}
                        </p>
                        <p><strong>⏰ Pickup Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
                        <p><strong>📍 Location:</strong> City Agriculture Office, San Pedro, Laguna</p>
                        <hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">
                    @endif
                    <p>• Bring a valid government-issued ID when picking up your items</p>
                    <p>• Please pickup within the 30-day window to avoid forfeiture</p>
                    <p>• Contact us if you need to reschedule or have concerns</p>
                @elseif($applicationType === 'rsbsa')
                    <p>• You will receive an SMS notification with the pickup date and time</p>
                    <p>• Bring a valid ID when picking up your seedlings</p>
                    <p>• Pickup location: City Agriculture Office</p>
                @elseif($applicationType === 'rsbsa')
                    <p>• Your RSBSA card will be processed and made available for pickup</p>
                    <p>• You will receive another notification when your card is ready</p>
                    <p>• Bring a valid ID when picking up your RSBSA card</p>
                @elseif($applicationType === 'fishr')
                    <p>• Your FishR registration is now active</p>
                    <p>• You can now apply for BoatR registration if you have a fishing vessel</p>
                    <p>• Keep your registration number for future transactions</p>
                @elseif($applicationType === 'boatr')
                    <p>• Your boat registration is being processed</p>
                    <p>• An inspection may be scheduled for your vessel</p>
                    <p>• You will receive further instructions via SMS and email</p>
                @endif
            </div>

            <p>If you have any questions or concerns, please don't hesitate to contact our office.</p>

            <p>Thank you for choosing our services!</p>

            <p>Best regards,<br>
                <strong>City Agriculture Office</strong><br>
                San Pedro, Laguna
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>City Agriculture Office | San Pedro, Laguna</p>
            <p>Contact: (123) 456-7890 | Email: agriculture@sanpedro.gov.ph</p>
        </div>
    </div>
</body>

</html>
