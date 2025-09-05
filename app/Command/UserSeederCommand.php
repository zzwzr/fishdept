<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Faker\Factory;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class UserSeederCommand extends HyperfCommand
{
    protected $userRepository;
    
    protected $faker;

    public function __construct(protected ContainerInterface $container, UserRepository $userRepository)
    {
        parent::__construct('user:seed');

        $this->userRepository = $userRepository;
        $this->faker = Factory::create('zh_CN');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('生成测试用户数据');

        $this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '生成用户数量', 20);
    }

    public function handle()
    {
        $count = (int) $this->input->getOption('count');
        
        $this->info("开始生成 {$count} 个测试用户...");
        
        $bar = $this->output->createProgressBar($count);
        
        for ($i = 0; $i < $count; $i++) {
            $userData = $this->generateUserData();
            
            try {
                $this->userRepository->create($userData);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("创建用户失败: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->info("\n成功生成 {$count} 个测试用户！");
    }

    protected function generateUserData(): array
    {
        return [
            'mobile'        => $this->faker->unique()->phoneNumber,
            'name'          => $this->faker->name,
            'password'      => password_hash('123123', PASSWORD_DEFAULT),
            'avatar'        => $this->faker->imageUrl(100, 100, 'people'),
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
    }
}
