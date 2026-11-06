# Impact Seeder Script
# Seeds the database with curated media and demo accounts

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "        SEEDING IMPACT VIA DOCKER         " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

docker exec impact_app php artisan db:seed

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "       SEED COMPLETED SUCCESSFULLY!       " -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "URL:         http://localhost:8000" -ForegroundColor White
Write-Host "Admin:       admin@impact.test / Password123456!" -ForegroundColor White
Write-Host "Demo User:   demo@impact.test  / Password123456!" -ForegroundColor White
