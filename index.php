<?php 
session_start(); 
require 'db.php';

$availableSlots = [];
$lunch_start = "13:00:00";
$lunch_end = "14:00:00";

// Generate slots for the next 7 days
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i day"));
    for ($h = 9; $h < 17; $h++) {
        $time = sprintf("%02d:00:00", $h);
        if ($time >= $lunch_start && $time < $lunch_end) continue;

        $slot = "$date $time";

        // Check if booked
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE slot_time = ?");
        $stmt->execute([$slot]);
        $alreadyBooked = $stmt->fetchColumn();

        // Check if blocked
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blocked_slots WHERE block_date = ? AND block_time = ?");
        $stmt->execute([$date, $time]);
        $isBlocked = $stmt->fetchColumn();

        if ($alreadyBooked == 0 && $isBlocked == 0) {
            $availableSlots[$date][] = $time;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentist Booking - Premium Care</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            padding: 20px;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
            50% { background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            animation: titleBounce 1s ease-out 0.5s both;
        }

        @keyframes titleBounce {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
            animation: fadeIn 1s ease-out 0.8s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .date-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            opacity: 0;
            animation: slideInUp 0.6s ease-out forwards;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .date-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .date-card:nth-child(1) { animation-delay: 0.1s; }
        .date-card:nth-child(2) { animation-delay: 0.2s; }
        .date-card:nth-child(3) { animation-delay: 0.3s; }
        .date-card:nth-child(4) { animation-delay: 0.4s; }
        .date-card:nth-child(5) { animation-delay: 0.5s; }
        .date-card:nth-child(6) { animation-delay: 0.6s; }
        .date-card:nth-child(7) { animation-delay: 0.7s; }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .date-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .date-icon {
            font-size: 1.5em;
            color: #667eea;
            margin-right: 12px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .date-text {
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
        }

        .slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }

        .slot {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 8px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .slot::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .slot:hover::before {
            left: 100%;
        }

        .slot:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(45deg, #764ba2, #667eea);
        }

        .slot:active {
            transform: translateY(0) scale(1);
        }

        .no-slots {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-out;
        }

        .no-slots i {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .no-slots h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .no-slots p {
            color: #666;
            font-size: 1.1em;
        }

        .footer-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border: 2px solid transparent;
        }

        .nav-link:hover {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .nav-link i {
            font-size: 1.2em;
        }

        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            color: white;
            animation: spin 1s linear infinite;
            z-index: 1000;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .morning-slots, .afternoon-slots {
            margin-bottom: 15px;
        }

        .time-period {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .calendar-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 15px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .slots-container {
                grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            }
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            animation: slideInRight 0.5s ease-out;
            z-index: 1000;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tooth"></i> Premium Dental Care</h1>
            <p>Book your appointment with our expert dentists</p>
        </div>

        <?php if ($availableSlots): ?>
            <div class="calendar-grid">
                <?php foreach ($availableSlots as $date => $slots): ?>
                    <div class="date-card">
                        <div class="date-header">
                            <i class="fas fa-calendar-day date-icon"></i>
                            <div class="date-text">
                                <?= date("l, d M Y", strtotime($date)) ?>
                            </div>
                        </div>
                        
                        <?php
                        $morningSlots = array_filter($slots, function($time) {
                            return strtotime($time) < strtotime('13:00:00');
                        });
                        $afternoonSlots = array_filter($slots, function($time) {
                            return strtotime($time) >= strtotime('14:00:00');
                        });
                        ?>
                        
                        <?php if ($morningSlots): ?>
                            <div class="morning-slots">
                                <div class="time-period">
                                    <i class="fas fa-sun"></i> Morning
                                </div>
                                <div class="slots-container">
                                    <?php foreach ($morningSlots as $time): ?>
                                        <a class="slot" href="book_appointment.php?date=<?= $date ?>&time=<?= $time ?>">
                                            <?= date("h:i A", strtotime($time)) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($afternoonSlots): ?>
                            <div class="afternoon-slots">
                                <div class="time-period">
                                    <i class="fas fa-cloud-sun"></i> Afternoon
                                </div>
                                <div class="slots-container">
                                    <?php foreach ($afternoonSlots as $time): ?>
                                        <a class="slot" href="book_appointment.php?date=<?= $date ?>&time=<?= $time ?>">
                                            <?= date("h:i A", strtotime($time)) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-slots">
                <i class="fas fa-calendar-times"></i>
                <h3>No Available Slots</h3>
                <p>Please check back later or contact us directly</p>
            </div>
        <?php endif; ?>

        <div class="footer-nav">
            <div class="nav-links">
                <a href="login.php" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    Patient Login
                </a>
                <a href="register.php" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
                <a href="admin_login.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    Admin Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation on slot click
        document.querySelectorAll('.slot').forEach(slot => {
            slot.addEventListener('click', function(e) {
                const spinner = document.createElement('div');
                spinner.className = 'loading-spinner';
                spinner.innerHTML = '<i class="fas fa-spinner"></i>';
                document.body.appendChild(spinner);
                
                setTimeout(() => {
                    spinner.remove();
                }, 1000);
            });
        });

        // Add smooth scroll effect
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.date-card');
            
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            cards.forEach(card => {
                observer.observe(card);
            });
        });

        // Add particle effect on hover
        document.querySelectorAll('.slot').forEach(slot => {
            slot.addEventListener('mouseenter', function() {
                this.style.background = 'linear-gradient(45deg, #764ba2, #667eea)';
            });
            
            slot.addEventListener('mouseleave', function() {
                this.style.background = 'linear-gradient(45deg, #667eea, #764ba2)';
            });
        });
    </script>
</body>
</html>