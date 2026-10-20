<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Media;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $avatarPath = storage_path('app/public/avatars');
        $coverPath = storage_path('app/public/covers');
        $mediaPath = storage_path('app/public/media');

        // Helper to list filenames in a public storage folder
        $getFiles = function ($dir) {
            if (!File::isDirectory($dir)) {
                return [];
            }
            return collect(File::files($dir))
                ->map(fn($f) => $f->getFilename())
                ->filter(fn($f) => !str_starts_with($f, '.'))
                ->values()
                ->all();
        };

        $avatarFiles = $getFiles($avatarPath);
        $coverFiles = $getFiles($coverPath);
        $mediaFiles = $getFiles($mediaPath);

        $getAvatar = fn($idx) => !empty($avatarFiles) 
            ? 'avatars/' . $avatarFiles[$idx % count($avatarFiles)] 
            : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80';

        $getCover = fn($idx) => !empty($coverFiles) 
            ? 'covers/' . $coverFiles[$idx % count($coverFiles)] 
            : null;

        $defaultPassword = Hash::make('Password123456!');

        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@impact.test'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Impact',
                'username' => 'admin',
                'password' => $defaultPassword,
                'role' => 'admin',
                'is_active' => true,
                'avatar' => $getAvatar(0),
                'cover' => $getCover(0),
            ]
        );

        // 2. Demo User
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@impact.test'],
            [
                'first_name' => 'Alex',
                'last_name' => 'Rivera',
                'username' => 'demouser',
                'password' => $defaultPassword,
                'role' => 'user',
                'is_active' => true,
                'avatar' => $getAvatar(1),
                'cover' => $getCover(1),
            ]
        );

        // 3. Creator Community Users
        $creatorsData = [
            ['first_name' => 'Elena', 'last_name' => 'Rostova', 'username' => 'elena_art', 'email' => 'elena@impact.test'],
            ['first_name' => 'Marcus', 'last_name' => 'Chen', 'username' => 'marcus_design', 'email' => 'marcus@impact.test'],
            ['first_name' => 'Sarah', 'last_name' => 'Jenkins', 'username' => 'sarah_clicks', 'email' => 'sarah@impact.test'],
            ['first_name' => 'Liam', 'last_name' => "O'Connor", 'username' => 'liam_visuals', 'email' => 'liam@impact.test'],
            ['first_name' => 'Yasmine', 'last_name' => 'Alami', 'username' => 'yasmine_studio', 'email' => 'yasmine@impact.test'],
            ['first_name' => 'Karim', 'last_name' => 'Benali', 'username' => 'karim_motion', 'email' => 'karim@impact.test'],
            ['first_name' => 'Sofia', 'last_name' => 'Rossi', 'username' => 'sofia_lens', 'email' => 'sofia@impact.test'],
            ['first_name' => 'David', 'last_name' => 'Miller', 'username' => 'david_craft', 'email' => 'david@impact.test'],
        ];

        $creators = collect();
        foreach ($creatorsData as $idx => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'username' => $data['username'],
                    'password' => $defaultPassword,
                    'role' => 'user',
                    'is_active' => true,
                    'avatar' => $getAvatar($idx + 2),
                    'cover' => $getCover($idx + 2),
                ]
            );
            $creators->push($user);
        }

        $allUsers = collect([$demoUser])->merge($creators);

        // 4. Tags
        $tagNames = [
            'Design', 'Architecture', 'Photography', 'Typography',
            'Minimalism', 'Nature', 'UI/UX', 'Abstract', 'Travel', 'Art',
            'Motion', 'Editorial', '3D', 'Aesthetic'
        ];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::firstOrCreate(['name' => $name]));
        }

        // 5. Follow Relationships
        if ($creators->count() >= 4) {
            $demoUser->following()->syncWithoutDetaching([$creators[0]->id, $creators[1]->id, $creators[2]->id]);
            $creators[0]->following()->syncWithoutDetaching([$demoUser->id, $creators[1]->id]);
            $creators[1]->following()->syncWithoutDetaching([$demoUser->id, $creators[3]->id]);
            $creators[2]->following()->syncWithoutDetaching([$demoUser->id]);
            $creators[3]->following()->syncWithoutDetaching([$demoUser->id, $creators[0]->id]);
        }

        // 6. Curated Albums for Users
        $albumsData = [
            ['title' => 'Minimal Architecture & Forms', 'description' => 'Clean geometric facades, concrete textures, and structural purity.', 'is_private' => false],
            ['title' => 'Golden Hour & Street Vibes', 'description' => 'Atmospheric shadows, warm sunset palettes, and urban scenes.', 'is_private' => false],
            ['title' => 'Editorial Typography & Print', 'description' => 'Swiss-style layouts, bold serif pairings, and magazine spreads.', 'is_private' => false],
            ['title' => 'Abstract Motion & 3D Loops', 'description' => 'Prismatic refractions, kinetic typography, and fluid dynamics.', 'is_private' => false],
            ['title' => 'Nordic Living & Warm Spaces', 'description' => 'Interior architecture, oak minimalism, and daylight harmony.', 'is_private' => false],
        ];

        $createdAlbums = collect();
        foreach ($albumsData as $idx => $albumInfo) {
            $owner = $idx === 0 ? $demoUser : $creators[$idx % $creators->count()];
            $album = Album::firstOrCreate(
                ['title' => $albumInfo['title'], 'user_id' => $owner->id],
                [
                    'description' => $albumInfo['description'],
                    'cover_image' => $getCover($idx),
                    'is_private' => $albumInfo['is_private'],
                ]
            );
            $createdAlbums->push($album);
        }

        // 7. Posts & Media
        $postTitles = [
            ['title' => 'Nordic Architecture & Clean Lines', 'desc' => 'Exploring minimal geometric facades in modern Scandinavian residential architecture.', 'tags' => ['Architecture', 'Minimalism', 'Design']],
            ['title' => 'Golden Hour in Kyoto', 'desc' => 'Atmospheric sunset light passing through bamboo groves and traditional timber shrines.', 'tags' => ['Photography', 'Travel', 'Nature']],
            ['title' => 'Editorial Typography System', 'desc' => 'Swiss-style high contrast serif and sans-serif pairings for contemporary magazines.', 'tags' => ['Typography', 'Design', 'Editorial']],
            ['title' => 'Abstract Glass & Light Refraction', 'desc' => 'Macro studio photography exploring fluid prism reflections through textured optical glass.', 'tags' => ['Abstract', 'Art', 'Photography']],
            ['title' => 'Minimalist Workspace Setup', 'desc' => 'Oak wood desk, natural ambient lighting, and clutter-free hardware ergonomics.', 'tags' => ['Design', 'Minimalism', 'UI/UX']],
            ['title' => 'Brutalist Concrete Textures', 'desc' => 'Monolithic forms and weathered board-formed concrete in urban municipal spaces.', 'tags' => ['Architecture', 'Photography']],
            ['title' => 'Ceramic Glaze Studies', 'desc' => 'Handcrafted stoneware featuring reactive crackle glazes and earthy volcanic pigments.', 'tags' => ['Art', 'Design', 'Aesthetic']],
            ['title' => 'Misty Alpine Ridge', 'desc' => 'Early morning fog settling across jagged peaks in the Dolomites at 2,400m altitude.', 'tags' => ['Nature', 'Travel', 'Photography']],
            ['title' => 'Kinetic Motion Typography', 'desc' => 'Dynamic variable font animations blending fluid transitions with bold typography.', 'tags' => ['Motion', 'Typography', 'Design']],
            ['title' => 'Prismatic Fluid Simulation', 'desc' => 'High frame-rate iridescent fluid dynamics in procedural 3D environments.', 'tags' => ['Motion', '3D', 'Abstract']],
            ['title' => 'Urban Midnight Walk', 'desc' => 'Reflections on wet asphalt under vibrant neon signs and low night fog.', 'tags' => ['Photography', 'Aesthetic', 'Travel']],
            ['title' => 'Contemporary Editorial Layout', 'desc' => 'Asymmetric grids and expansive whitespace showcasing contemporary fine art.', 'tags' => ['Editorial', 'Design', 'Minimalism']],
        ];

        $createdPosts = collect();

        if (!empty($mediaFiles)) {
            foreach ($mediaFiles as $idx => $filename) {
                $author = $allUsers[$idx % $allUsers->count()];
                $info = $postTitles[$idx % count($postTitles)];
                $isVideo = str_ends_with(strtolower($filename), '.mp4');

                $post = Post::create([
                    'user_id' => $author->id,
                    'title' => $info['title'] . ($idx >= count($postTitles) ? ' #' . ($idx + 1) : ''),
                    'description' => $info['desc'],
                    'status' => Post::STATUS_PUBLIC,
                ]);

                Media::create([
                    'post_id' => $post->id,
                    'user_id' => $author->id,
                    'path' => 'media/' . $filename,
                    'type' => $isVideo ? 'video' : 'image',
                ]);

                $tagIds = $tags->whereIn('name', $info['tags'])->pluck('id');
                if ($tagIds->isNotEmpty()) {
                    $post->tags()->sync($tagIds);
                }

                // Add to corresponding album
                $targetAlbum = $createdAlbums[$idx % $createdAlbums->count()];
                $targetAlbum->posts()->syncWithoutDetaching([$post->id]);

                $createdPosts->push($post);
            }
        }

        // 8. Comments
        $sampleComments = [
            'Incredible color palette and composition!',
            'The texture and depth in this shot are stunning.',
            'Bookmarking this for my upcoming design project reference.',
            'Love the lighting here. Truly inspiring work!',
            'The contrast and whitespace balance are exceptional.',
            'Such clean execution and visual storytelling.',
        ];

        foreach ($createdPosts->take(15) as $post) {
            $commenters = $allUsers->where('id', '!=', $post->user_id)->random(min(2, $allUsers->count() - 1));
            foreach ($commenters as $commenter) {
                Comment::create([
                    'user_id' => $commenter->id,
                    'post_id' => $post->id,
                    'content' => $sampleComments[array_rand($sampleComments)],
                ]);
            }
        }

        // 9. Likes & Saved Posts for Demo User
        foreach ($createdPosts as $idx => $post) {
            if ($idx % 2 === 0) {
                Like::firstOrCreate([
                    'user_id' => $demoUser->id,
                    'post_id' => $post->id,
                ]);
            }
            if ($idx % 3 === 0) {
                SavedPost::firstOrCreate([
                    'user_id' => $demoUser->id,
                    'post_id' => $post->id,
                ]);
            }
        }

        $this->command->info('Impact database successfully seeded with ' . $createdPosts->count() . ' posts and local media!');
    }
}
