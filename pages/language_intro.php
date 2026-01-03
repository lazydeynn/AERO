<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['lang_id'])) {
    header("Location: languages.php");
    exit;
}

$lang_id = (int)$_GET['lang_id'];

$sql = "SELECT * FROM languages WHERE language_id = $lang_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) die("Language not found.");
$lang = $result->fetch_assoc();
$name = $lang['name'];
$safe_name = strtolower(str_replace('#', 's', $name)); // e.g. "c#" -> "cs", "python" -> "python"

$content = [
    'Python' => [
        'hero_desc' => 'Python is a high-level, interpreted, and general-purpose programming language known for its simplicity, readability, and versatility. It allows developers to write code that is easy to understand and maintain, making it ideal for beginners and professionals alike.',
        'creator_name' => 'Guido Van Rossum',
        'creator_bio' => 'A Dutch computer programmer. He began working on Python in the late 1980s at Centrum Wiskunde & Informatica (CWI) in the Netherlands, with the first version, Python 0.9.0, released in 1991. Van Rossum designed Python as a successor to the ABC programming language, aiming for a language that was easy to read, write, and understand, with a focus on code readability and simplicity. The name "Python" was inspired by the British comedy show Monty Python\'s Flying Circus.',
        'origin_text' => 'Python was created by Guido van Rossum in the Netherlands, initially as a hobby project during the 1989 Christmas holidays. He was inspired by the British comedy series Monty Python\'s Flying Circus and named the language after it. Python was designed to be a highly readable and simple language that would help programmers express concepts in fewer lines of code.',
        'benefits' => [
            ['title' => 'Easy to learn and read', 'desc' => 'Python has a simple and clean syntax that reads almost like plain English. This makes it beginner-friendly and allows you to focus on learning programming logic instead of complex syntax.', 'class' => 'col-span-1'],
            ['title' => 'Large community and library support', 'desc' => 'Python has a massive global community, meaning help is always available through forums and tutorials. It also comes with countless libraries and frameworks that make coding faster and easier.', 'class' => 'col-span-1'],
            ['title' => 'Great for automation and Productivity', 'desc' => 'With Python, you can easily automate repetitive computer tasks like file management or data entry. This not only saves time but also helps you build efficient workflows and focus on more important tasks.', 'class' => 'col-span-1 row-span-2'], // Tall card
            ['title' => 'High demand in the job market', 'desc' => 'Many companies use Python for software, AI, and data-driven applications. Knowing Python can greatly increase your job opportunities and make your resume stand out.', 'class' => 'col-span-1'],
            ['title' => 'Versatile and Powerful', 'desc' => 'Python can be used in a wide range of fields such as web development, data analysis, artificial intelligence, automation, and game development. Learning it gives you flexibility to work on different kinds of projects with one language.', 'class' => 'col-span-2'] // Wide card
        ]
    ],
    'C#' => [
        'hero_desc' => 'C# (pronounced "C-Sharp") is a modern, object-oriented programming language developed by Microsoft as part of the .NET framework. It is designed for building a wide range of applications - from desktop and web apps to mobile games and enterprise software.',
        'creator_name' => 'Anders Hejlsberg',
        'creator_bio' => 'The primary creator and lead architect of the C# programming language is Anders Hejlsberg. He led the team at Microsoft that developed the language, which was first widely distributed in July 2000. Hejlsberg is also known for his work on other significant programming languages and development tools, including Turbo Pascal, Delphi, and TypeScript.',
        'origin_text' => 'It was designed to be a modern, general-purpose language suitable for a wide range of applications, from desktop and web applications to games and mobile development, within the managed execution environment of the .NET platform.',
        'benefits' => [
            ['title' => 'Modern and Easy to Learn', 'desc' => 'C# has a clean, readable syntax that\'s beginner-friendly yet powerful. It\'s a great starting language for developers who want to learn modern programming concepts.', 'class' => 'col-span-1'],
            ['title' => 'Versatile and Multi-Purpose', 'desc' => 'C# can be used for almost anything — from web apps and desktop software to mobile and game development (with Unity). It\'s truly a one-language-fits-all solution.', 'class' => 'col-span-1'],
            ['title' => 'Strong Integration with .NET', 'desc' => 'C# works hand-in-hand with Microsoft\'s .NET ecosystem, which provides extensive libraries, APIs, and tools to build robust and scalable applications.', 'class' => 'col-span-1 row-span-2'],
            ['title' => 'High demand and Career Growth', 'desc' => 'C# developers are in strong demand in software, web, and game development industries. Its stability, support, and versatility make it a great long-term skill to master.', 'class' => 'col-span-1'],
            ['title' => 'Excellent for Game Development', 'desc' => 'C# is the primary language used in Unity, one of the world\'s most popular game engines — powering thousands of indie and AAA games.', 'class' => 'col-span-2']
        ]
    ],
    'C++' => [
        'hero_desc' => ' C++ is a powerful, general-purpose programming language created by Bjarne Stroustrup. It is widely used for system/software development, game programming, and performance-critical applications.',
        'creator_name' => 'Bjarne Stroustrup',
        'creator_bio' => 'The creator of the C++ programming language is Bjarne Stroustrup. He began developing C++ in 1979 at Bell Labs as an extension of the C language, adding object-oriented and generic programming features while maintaining C\'s efficiency and flexibility.',
        'origin_text' => ' C++ was designed as a middle-level language combining the efficiency of low-level programming with the abstraction of high-level programming. It is widely used in operating systems, game engines, embedded systems, and real-time simulations.',
        'benefits' => [
            ['title' => 'High Demand and Career Opportunities', 'desc' => 'C++ developers are in demand in industries like game development, finance, robotics, and embedded systems. Mastering C++ can open doors to well-paying and technically challenging roles.', 'class' => 'col-span-1'],
            ['title' => 'Large community and library support', 'desc' => 'C++ has a vast global community and extensive libraries for nearly every purpose — from graphics (OpenGL, SFML) to machine learning (MLPack) and data structures (STL). Support and resources are always easy to find.', 'class' => 'col-span-1'],
            ['title' => 'Strong Foundation for Other Languages', 'desc' => 'Learning C++ builds a deep understanding of programming concepts like memory management, pointers, and object-oriented design — which helps when learning languages such as C#, Java, or even Python.', 'class' => 'col-span-1 row-span-2'],
            ['title' => 'Fast and Effecient', 'desc' => 'C++ is known for its high performance and speed. It gives programmers fine control over system resources and memory, making it ideal for developing operating systems, game engines, and real-time simulations.', 'class' => 'col-span-1'],
            ['title' => 'Great for System and Game Development', 'desc' => 'C++ is the backbone of many operating systems, browsers, and major game engines like Unreal Engine. Its speed and efficiency make it perfect for performance-critical applications.', 'class' => 'col-span-2']
        ]
    ],
    'JavaScript' => [
        'hero_desc' => 'JavaScript is a versatile, high-level programming language primarily used to create interactive and dynamic web pages. It runs directly in the browser and powers most of the modern web.',
        'creator_name' => 'Brendan Eich',
        'creator_bio' => 'The creator of the JavaScript programming language is Brendan Eich. He developed JavaScript in 1995 while working at Netscape Communications. It was originally designed to make web pages more interactive. Despite being created in just 10 days, JavaScript has evolved into a powerful, full-featured programming language that drives the modern internet.',
        'origin_text' => 'JavaScript is a high-level, interpreted scripting language used to create dynamic and interactive effects within web browsers. It allows developers to build interactive elements such as animations, form validations, and real-time updates without reloading web pages. Today, it’s also used in mobile, desktop, and server-side applications through frameworks like React, Angular, and Node.js.',
        'benefits' => [
            ['title' => 'Easy to Learn and User', 'desc' => 'JavaScript has simple, beginner-friendly syntax and runs directly in any browser without installation. You can start coding and seeing results immediately.', 'class' => 'col-span-1'],
            ['title' => 'Runs Everywhere', 'desc' => 'JavaScript works across all major web browsers and platforms. With frameworks like Node.js, you can also use it for backend development, mobile apps, and even IoT.', 'class' => 'col-span-1'],
            ['title' => 'Large Community and Ecosystem', 'desc' => 'It has one of the largest developer communities and ecosystems. There are countless tutorials, forums, and open-source libraries that make learning and development faster.', 'class' => 'col-span-1 row-span-2'],
            ['title' => 'High Demand in the Job Market', 'desc' => 'Because it\'s essential for web development, JavaScript developers are always in demand — both for front-end and full-stack roles.', 'class' => 'col-span-1'],
            ['title' => 'Versatile and Powerful', 'desc' => 'JavaScript can be used for almost anything: building websites, web apps, mobile apps (with React Native), desktop apps (with Electron), and server-side software. It\'s one language that can do it all.', 'class' => 'col-span-2']
        ]
    ],
    'Java' => [
        'hero_desc' => 'Java is a high-level, class-based, object-oriented programming language designed to have as few implementation dependencies as possible. It is widely used for building enterprise applications, Android apps, desktop software, and web servers. ',
        'creator_name' => 'James Gosling',
        'creator_bio' => 'The creator of the Java programming language is James Gosling. He developed Java in 1995 while working at Sun Microsystems. His goal was to create a language that could run on any device regardless of hardware or operating system. Java was originally designed for interactive television but soon became a dominant language for business and web applications.',
        'origin_text' => 'Java is a platform-independent, object-oriented programming language that runs on the Java Virtual Machine (JVM). This means Java code can run on any device or operating system that has a JVM installed — making it portable and efficient. It’s used in a wide variety of fields, including enterprise software, Android development, and large-scale backend systems.',
        'benefits' => [
            ['title' => 'Platform Independence', 'desc' => 'Java\'s “write once, run anywhere” feature allows programs to run on any device with a JVM, making it ideal for cross-platform development.', 'class' => 'col-span-1'],
            ['title' => 'Object-Oriented and Structured', 'desc' => 'Java promotes clean, modular, and reusable code through its object-oriented structure, which makes large projects easier to manage and maintain.', 'class' => 'col-span-1'],
            ['title' => 'Huge Community and Library Support', 'desc' => 'Java has one of the largest developer communities and extensive libraries, frameworks, and APIs (like Spring, Hibernate, and JavaFX) that speed up development.', 'class' => 'col-span-1 row-span-2'],
            ['title' => 'Secure and Stable', 'desc' => 'Java provides built-in security features and memory management, making it highly reliable for enterprise-level and mission-critical applications.', 'class' => 'col-span-1'],
            ['title' => 'High Demand in the Job Market', 'desc' => 'Java developers are in demand in industries like finance, enterprise solutions, Android development, and cloud computing — offering strong career opportunities.', 'class' => 'col-span-2']
        ]
    ],
    'Dart' => [
        'hero_desc' => 'Dart is a modern, object-oriented programming language developed by Google. It\'s optimized for building fast, scalable, and cross-platform applications. Dart is the primary language used with Flutter, Google\'s UI toolkit for creating beautiful mobile, web, and desktop apps from a single codebase. It combines performance, simplicity, and productivity — making it a great choice for modern app development.',
        'creator_name' => 'Lars Bak and Kasper Lund',
        'creator_bio' => 'The creators of the Dart programming language are Lars Bak and Kasper Lund, both engineers at Google. Dart was first introduced in 2011 as a language for the modern web and later evolved into a powerful tool for app development with the introduction of Flutter. Bak and Lund designed Dart to be fast, productive, and easy to learn for developers coming from JavaScript, Java, or C#.',
        'origin_text' => 'Dart is a client-optimized, class-based, object-oriented programming language with C-style syntax. It compiles both to native code (for mobile and desktop apps) and JavaScript (for web apps). Dart emphasizes performance and developer productivity, with features like hot reload, strong typing, and asynchronous programming support.',
        'benefits' => [
            ['title' => 'Perfect for Cross-Platform Development', 'desc' => 'With Flutter, Dart allows developers to create apps for Android, iOS, web, desktop, and even embedded systems — all from a single codebase.', 'class' => 'col-span-1'],
            ['title' => 'Fast and Efficient', 'desc' => 'Dart compiles to native ARM and x64 code, delivering high performance similar to languages like Swift or Kotlin. It also supports just-in-time (JIT) and ahead-of-time (AOT) compilation for speed and flexibility.', 'class' => 'col-span-1'],
            ['title' => 'Easy to Learn', 'desc' => 'Dart\'s syntax is simple and familiar to developers who know JavaScript, Java, or C#. This makes it beginner-friendly while still being powerful enough for professionals.', 'class' => 'col-span-1 row-span-2'],
            ['title' => 'High Demand in Mobile Development', 'desc' => 'As Flutter\'s popularity continues to rise, so does the demand for Dart developers. Learning Dart opens up career opportunities in mobile app development and beyond.', 'class' => 'col-span-1'],
            ['title' => 'Strong Community and Backing by Google', 'desc' => 'Dart and Flutter are maintained by Google and supported by a rapidly growing developer community, with plenty of libraries, tools, and resources.', 'class' => 'col-span-2']
        ]
    ],

];

$data = $content[$name];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $name; ?> - Introduction</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            background-color: #2b2e35;
            background-image: url('../assets/images/language_hero/<?php echo $safe_name; ?>_hero_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="bg-white">

    <?php include('../components/navbar.php') ?>

    <div class="relative hero-section min-h-[600px] flex flex-col items-center justify-center text-center px-6 py-20 text-white">

        <a href="languages.php" class="absolute top-8 left-8 text-white/80 hover:text-white transition">
            <i class="fa-solid fa-arrow-left text-2xl"></i>
        </a>

        <h1 class="text-7xl md:text-9xl font-bold mb-6 tracking-tight uppercase drop-shadow-lg">
            <?php echo $name; ?>
        </h1>

        <p class="max-w-4xl text-sm md:text-base text-gray-200 leading-relaxed mb-10 font-light">
            <?php echo $data['hero_desc']; ?>
        </p>

        <a href="topic_list.php?lang_id=<?php echo $lang_id; ?>" class="bg-[#2563eb] hover:bg-blue-600 text-white font-bold py-3 px-12 rounded-full shadow-lg transition transform hover:scale-105">
            Learn <?php echo $name; ?>
        </a>
    </div>

    <div class="max-w-7xl mx-auto px-8 py-32 flex flex-col md:flex-row items-center gap-20">

        <div class="md:w-1/2 order-2 md:order-1">
            <h2 class="text-4xl font-bold text-gray-900 mb-8"><?php echo $data['creator_name']; ?></h2>
            <p class="text-gray-600 text-sm leading-8 text-justify">
                <?php echo $data['creator_bio']; ?>
            </p>
        </div>

        <div class="md:w-1/2 flex justify-center order-1 md:order-2">
            <div class="relative">
                <img src="../assets/images/language_creator/creator_<?php echo $safe_name; ?>.png"
                    alt="<?php echo $data['creator_name']; ?>"
                    class="w-full max-w-sm grayscale hover:grayscale-0 transition duration-700 object-contain">
            </div>
        </div>
    </div>

    <div class="bg-[#1e1e1e] text-white py-32">
        <div class="max-w-4xl mx-auto px-8 text-center">
            <h3 class="text-2xl font-bold mb-8">Origin of <?php echo $name; ?></h3>
            <p class="text-gray-400 text-sm leading-8">
                <?php echo $data['origin_text']; ?>
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 py-32">
        <h3 class="text-center text-2xl font-bold text-gray-800 mb-16">Benefits of Learning <?php echo $name; ?></h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($data['benefits'] as $benefit): ?>
                <div class="bg-[#2d2d2d] p-8 rounded-2xl text-white shadow-lg hover:-translate-y-1 transition duration-300 flex flex-col justify-center <?php echo $benefit['class']; ?>">
                    <h4 class="font-bold text-lg mb-4 text-white leading-tight">
                        <?php echo $benefit['title']; ?>
                    </h4>
                    <p class="text-gray-400 text-[11px] leading-relaxed">
                        <?php echo $benefit['desc']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="h-20"></div>

    <?php include('../components/footer.php') ?>

</body>

</html>