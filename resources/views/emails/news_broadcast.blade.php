<!DOCTYPE html>
<html>
<head>
    <title>{{ $news->title }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color: #c53030; text-transform: uppercase;">Flash 360 Degree Update</h2>

        @if($news->image)
            <img src="{{ asset('storage/' . $news->image) }}" style="width: 100%; border-radius: 8px; margin-top: 10px;" alt="News Image">
        @endif

        <h1 style="font-size: 24px; margin-top: 20px;">{{ $news->title }}</h1>
        <p style="color: #555; line-height: 1.6;">
            {{ Str::limit(strip_tags($news->content), 200) }}...
        </p>

        <div style="text-align: center; margin-top: 30px;">
            <a href="http://localhost:3000/news/{{ $news->slug }}" style="background-color: black; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 5px;">
                Read Full Story
            </a>
        </div>

        <hr style="margin-top: 30px; border: 0; border-top: 1px solid #eee;">
        <p style="text-align: center; font-size: 12px; color: #999;">
            You received this email because you subscribed to Flash 360 Degree.
        </p>
    </div>
</body>
</html>