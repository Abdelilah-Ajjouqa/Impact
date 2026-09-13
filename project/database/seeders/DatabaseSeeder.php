<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@impact.test'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Impact',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
            ]
        );

        // 2. Demo User for evaluation / reviewer login
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@impact.test'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Creator',
                'username' => 'demouser',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80',
            ]
        );

        // 3. Creator Community Users
        $creatorsData = [
            [
                'first_name' => 'Elena',
                'last_name' => 'Rostova',
                'username' => 'elena_art',
                'email' => 'elena@impact.test',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80',
            ],
            [
                'first_name' => 'Marcus',
                'last_name' => 'Chen',
                'username' => 'marcus_design',
                'email' => 'marcus@impact.test',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Jenkins',
                'username' => 'sarah_clicks',
                'email' => 'sarah@impact.test',
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&auto=format&fit=crop&q=80',
            ],
            [
                'first_name' => 'Liam',
                'last_name' => 'O\'Connor',
                'username' => 'liam_visuals',
                'email' => 'liam@impact.test',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&auto=format&fit=crop&q=80',
            ],
        ];

        $creators = collect();
        foreach ($creatorsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'username' => $data['username'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'is_active' => true,
                    'avatar' => $data['avatar'],
                ]
            );
            $creators->push($user);
        }

        $allUsers = collect([$demoUser])->merge($creators);

        // 4. Tags
        $tagNames = [
            'Design', 'Architecture', 'Photography', 'Typography',
            'Minimalism', 'Nature', 'UI/UX', 'Abstract', 'Travel', 'Art'
        ];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::firstOrCreate(['name' => $name]));
        }

        // 5. Follow Relationships (Social Graph)
        // Demo user follows 2 creators; 3 creators follow demo user
        if ($creators->count() >= 3) {
            $demoUser->following()->syncWithoutDetaching([$creators[0]->id, $creators[1]->id]);
            $creators[0]->following()->syncWithoutDetaching([$demoUser->id, $creators[2]->id]);
            $creators[1]->following()->syncWithoutDetaching([$demoUser->id]);
            $creators[2]->following()->syncWithoutDetaching([$demoUser->id]);
        }

        // 6. Curated Sample Posts with Media
        $samplePosts = [
            [
                'title' => 'Nordic Architecture & Clean Lines',
                'description' => 'Exploring minimal geometric facades in modern Scandinavian residential architecture.',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Architecture', 'Minimalism', 'Design'],
            ],
            [
                'title' => 'Golden Hour in Kyoto',
                'description' => 'Atmospheric sunset light passing through bamboo groves and traditional timber shrines.',
                'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Photography', 'Travel', 'Nature'],
            ],
            [
                'title' => 'Editorial Typography System',
                'description' => 'Exploring Swiss-style high contrast serif and sans-serif pairings for contemporary magazines.',
                'image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Typography', 'Design', 'UI/UX'],
            ],
            [
                'title' => 'Abstract Glass & Light Refraction',
                'description' => 'Macro studio photography exploring fluid prism reflections through textured optical glass.',
                'image' => 'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Abstract', 'Art', 'Photography'],
            ],
            [
                'title' => 'Minimalist Workspace Setup',
                'description' => 'Oak wood desk, natural ambient lighting, and clutter-free hardware ergonomics.',
                'image' => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Design', 'Minimalism'],
            ],
            [
                'title' => 'Brutalist Concrete Textures',
                'description' => 'Monolithic forms and weathered board-formed concrete in urban municipal spaces.',
                'image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Architecture', 'Photography'],
            ],
            [
                'title' => 'Ceramic Glaze Studies',
                'description' => 'Handcrafted stoneware featuring reactive crackle glazes and earthy volcanic pigments.',
                'image' => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Art', 'Design'],
            ],
            [
                'title' => 'Misty Alpine Ridge',
                'description' => 'Early morning fog settling across jagged peaks in the Dolomites at 2,400m altitude.',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800&auto=format&fit=crop&q=80',
                'tags' => ['Nature', 'Travel', 'Photography'],
            ],
        ];

        $createdPosts = collect();
        foreach ($samplePosts as $index => $postData) {
            $author = $allUsers[$index % $allUsers->count()];

            $post = Post::create([
                'user_id' => $author->id,
                'title' => $postData['title'],
                'description' => $postData['description'],
                'status' => Post::STATUS_PUBLIC,
            ]);

            // Attach Media
            Media::create([
                'post_id' => $post->id,
                'user_id' => $author->id,
                'path' => $postData['image'],
                'type' => 'image',
            ]);

            // Attach Tags
            $matchingTagIds = $tags->whereIn('name', $postData['tags'])->pluck('id');
            if ($matchingTagIds->isNotEmpty()) {
                $post->tags()->sync($matchingTagIds);
            }

            $createdPosts->push($post);
        }

        // 7. Comments
        $sampleComments = [
            'Incredible color palette and composition!',
            'The texture and depth in this shot are stunning.',
            'Bookmarking this for my upcoming design project reference.',
            'Love the lighting here. Truly inspiring work!',
        ];

        foreach ($createdPosts->take(4) as $post) {
            $commenter = $allUsers->where('id', '!=', $post->user_id)->random();
            Comment::create([
                'user_id' => $commenter->id,
                'post_id' => $post->id,
                'content' => $sampleComments[array_rand($sampleComments)],
            ]);
        }

        // 8. Saved Posts & Sample Album for Demo User
        if ($createdPosts->count() >= 3) {
            SavedPost::firstOrCreate([
                'user_id' => $demoUser->id,
                'post_id' => $createdPosts[0]->id,
            ]);
            SavedPost::firstOrCreate([
                'user_id' => $demoUser->id,
                'post_id' => $createdPosts[1]->id,
            ]);

            $album = Album::firstOrCreate(
                [
                    'user_id' => $demoUser->id,
                    'title' => 'Visual Inspiration 2025',
                ],
                [
                    'description' => 'A curated collection of design references and mood board imagery.'
                ]
            );

            $album->posts()->syncWithoutDetaching([$createdPosts[0]->id, $createdPosts[1]->id]);
        }
    }
}
