<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.5">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            background-color: #1e1e1e;
            background-image: url('../assets/images/elements/hero-bg.png');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .custom-shape-divider-bottom-1 {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .custom-shape-divider-bottom-1 svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 150px;
        }

        .custom-shape-divider-bottom-1 .shape-fill {
            fill: #FFFFFF;
        }

        .bg-contour {
            background-color: #ffffff;
            background-image: url('../assets/images/elements/devs-bg.png');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="bg-white text-gray-900">

    <?php include '../components/navbar.php'; ?>

    <section class="hero-section min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="w-full px-6 md:px-20 flex flex-col md:flex-row items-center justify-between z-10">

            <div class="md:w-1/2 text-white mt-10 md:mt-0">
                <h1 class="text-5xl md:text-7xl font-medium leading-tight mb-2">AI Educational</h1>
                <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">Resource Organizer</h1>

                <p class="text-gray-300 text-sm mb-10 max-w-lg font-light leading-relaxed">
                    AERO is your AI-powered online library that organizes educational resources, suggests related topics, and creates smart quizzes to make learning faster and easier.
                </p>

                <a href="languages.php" class="bg-white text-black px-10 py-4 rounded-full font-bold text-sm hover:bg-gray-200 transition shadow-lg">
                    Explore
                </a>
            </div>
        </div>
    </section>

    <section class="py-40 bg-white relative overflow-hidden">
        <img src="../assets/images/elements/pipe-left.svg" class="absolute left-0 top-1/2 transform -translate-y-1/2 w-64 opacity-90 hidden md:block pointer-events-none">
        <img src="../assets/images/elements/pipe-right.svg" class="absolute right-0 top-1/3 w-56 opacity-90 hidden md:block pointer-events-none">

        <div class="max-w-3xl mx-auto text-center px-6 relative z-10">
            <h2 class="text-5xl font-bold text-gray-900 mb-10">What is AERO?</h2>
            <p class="text-gray-600 text-base leading-8">
                AERO is a web-based learning platform designed to make programming education simple and interactive. Instead of long textbooks, we focus on specific topics that you can master quickly. Each lesson can be downloaded as a PDF, and after finishing, you’ll get a quiz to test your understanding.
            </p>
        </div>
    </section>

    <section class="bg-[#1e1e1e]">
        <div class="text-center py-24">
            <h2 class="text-white text-4xl font-bold">Application's Features</h2>
        </div>

        <div class="w-full">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 bg-[#1e1e1e] p-24 border-t border-gray-800 flex flex-col justify-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-1.5 bg-white mb-8"></div>
                        <h3 class="text-white font-bold text-3xl mb-6">AI Questionnaire generator</h3>
                        <p class="text-gray-400 text-sm leading-7">
                            Intelligently analyzes uploaded lessons or selected topics and automatically generates quizzes tailored to the student's learning needs. This allows users to review more effectively without manually creating practice questions.
                        </p>
                    </div>
                </div>
                <div class="md:w-1/2 bg-white p-24 flex flex-col justify-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-1.5 bg-black mb-8"></div>
                        <h3 class="text-gray-900 font-bold text-3xl mb-6">Progress Tracker</h3>
                        <p class="text-gray-600 text-sm leading-7">
                            Monitors quiz scores and study patterns over time. Students can track their improvement, check topic mastery levels, and identify areas that need more attention. This promotes consistent study habits and self-guided learning growth.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 bg-white p-24 flex flex-col justify-center order-2 md:order-1">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-1.5 bg-black mb-8"></div>
                        <h3 class="text-gray-900 font-bold text-3xl mb-6">Download Feature</h3>
                        <p class="text-gray-600 text-sm leading-7">
                            Learning resources such as reviewers, notes, and study guides are available in PDF format for easy downloading. This allows students to access their materials offline, anytime and anywhere — perfect for studying on-the-go or in low-internet areas.
                        </p>
                    </div>
                </div>
                <div class="md:w-1/2 bg-[#1e1e1e] p-24 border-t border-gray-800 flex flex-col justify-center order-1 md:order-2">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-1.5 bg-white mb-8"></div>
                        <h3 class="text-white font-bold text-3xl mb-6">Simple design and navigation</h3>
                        <p class="text-gray-400 text-sm leading-7">
                            AERO features a minimalist and intuitive design to ensure effortless navigation. The layout reduces visual clutter and focuses on clarity, making it easy for students to find what they need and stay focused while studying.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#1e1e1e] py-40">
        <div class="max-w-5xl mx-auto px-8 text-center">
            <h2 class="text-6xl font-bold text-white mb-20 leading-tight">Study Smarter,<br>Learn Better</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-20 text-left text-gray-400 text-sm leading-7 text-justify">
                <div>
                    <p class="mb-8">
                        AERO (AI Educational Resource Organizer) is designed to help students study smarter, not harder. We understand that learning can be overwhelming, especially when resources are scattered and reviewing takes time. That’s why we created a platform that centralizes study materials and uses AI to generate personalized quizzes, making learning more efficient and accessible.
                    </p>
                    <p>
                        Our mission is to support students in building better study habits through organization, guided practice, and progress awareness. With features like a clean and simple interface, PDF resource downloads, and a built-in progress tracker, AERO aims to create a learning environment that feels effortless and engaging.
                    </p>
                </div>
                <div>
                    <p class="mb-8">
                        It was developed with students in mind—especially those balancing multiple subjects, deadlines, and study loads. By simplifying access to learning materials and generating quizzes instantly, we help reduce stress and save valuable time. Our goal is to make studying feel lighter, more organized, and more effective, no matter the academic level.
                    </p>
                    <p>
                        We also believe education should be accessible anywhere. That’s why AERO allows students to download their study materials in PDF format for offline use and track their progress at their own pace. As we continue improving the platform, we aim to expand features and integrate more intelligent tools that empower learners to take control of their education and grow confidently.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-40">
        <div class="text-center mb-24">
            <h2 class="text-gray-900 text-4xl font-bold">Our Logo</h2>
        </div>

        <div class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row items-center gap-20">
            <div class="w-full md:w-1/2 flex justify-center items-center border border-gray-200 aspect-square shadow-sm p-16">
                <img src="../assets/images/logo/logo_black.svg" alt="A" class="w-3/4 h-auto ml-8">
            </div>

            <div class="w-full md:w-1/2">
                <img src="../assets/images/logo/aero_black.svg" alt="AERO" class="h-22 w-auto mb-12">
                <p class="text-gray-600 text-sm leading-7 mb-8">
                    The AERO logo represents clarity, structure, and modern learning. Its clean lines and balanced composition reflect the app’s core purpose organizing educational resources in a simple and efficient way. The soft, streamlined shapes convey approachability, indicating that learning with AERO is guided, supportive, and stress-free.
                </p>
                <p class="text-gray-600 text-sm leading-7">
                    The choice of color and typography reinforces professionalism while maintaining a friendly and accessible tone. The design symbolizes progress and forward movement, aligning with AERO’s goal of helping students grow academically at their own pace.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-[#1e1e1e] h-20 w-auto p-16"></section>
    <section class="bg-contour py-40">
        <div class="text-center mb-20">
            <img src="../assets/images/logo/aero_black.svg" alt="AERO" class="h-10 mx-auto mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Meet our Developers!</h2>
        </div>

        <div class="max-w-7xl mx-auto px-8 flex flex-wrap justify-center gap-16">
            <?php
            $devs = [
                ["name" => "Jared G. Barbiran", "img" => "jared.png"],
                ["name" => "Cedric Johanns C. Sorrera", "img" => "cedric.png"],
                ["name" => "Lemuel Dane G. Biala", "img" => "lemuel.png"],
                ["name" => "Ervin Hienz P. Cangco", "img" => "ervin.png"],
                ["name" => "Jiel Mayer L. Asunio", "img" => "mayer.png"]
            ];
            foreach ($devs as $d):
            ?>
                <div class="text-center flex flex-col items-center">
                    <div class="w-40 h-40 rounded-full bg-gray-100 mb-6 overflow-hidden border-4 border-white shadow-xl relative">
                        <img src="../assets/images/team_pfp/<?php echo $d['img']; ?>"
                            alt="<?php echo $d['name']; ?>"
                            class="w-full h-full object-cover">
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 tracking-wide"><?php echo $d['name']; ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>

</body>

</html>