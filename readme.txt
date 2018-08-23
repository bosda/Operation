运维平台SDK使用说明
运维平台url：operation.world

平台主要是用于上线项目发生异常时发送报告以及日志至项目负责人，以用于调试修复，不用上服务器查看log文件，提高效率。

人员可以在运维平台中绑定邮箱和微信，开启微信和邮箱通知，目前的通讯方式有站内信，邮箱，微信公众号通知。

PHP
在laravel 5+使用
exception报告
将文档中的ReportHelper.php(文档下载中下载)放到app/Utils下（已写命名空间，更改目录请自行修改命名空间），然后向管理员要项目的project_token，把SDK中的operation.php复制到配置config中，并填上project_token。operation.php内容如下：

//config.operation

return [

    'token' => '***',

    'report' => env('APP_OPERATION_REPORT', false),
];

最后修改app/Exceptions/Handler的report方法，修改如下：

//app/Exceptions/Handler

    use App\Utils\ReportHelper;

    public function report(Exception $e)
    {
        parent::report($e);

        //确保下面的代码在parent::report的执行之后执行。
        if ($this->shouldReport($e)) {
            ReportHelper::exception($e);
        }
    }

以上当项目发生异常时，并且未处理时就会记录发送异常。即记录进项目本身的laravel.log，和发送异常详情至运维平台。换句话说，只要laravel.log里有记录异常，就会发送报告至运维平台。

info报告
另外，在业务代码中，有时如果发生了某一情况时，你想收到通知，可以这样写：


    use App\Utils\ReportHelper;

    public function method(Exception $e)
    {
        //some code
        if (something) {
            ReportHelper::info('this is a message');
        }
    }

info方法第二个参数可以支持文件，只要你传入文件的绝对路径至第二参数即可，多个文件也可以传进数组，用法如下:


    use App\Utils\ReportHelper;

    //单文件
    ReportHelper::info('this is a message', stroage_path('some/files'));
    //多文件
    ReportHelper::info('this is a message', [stroage_path('some/files'), stroage_path('some/files')]);

logAndReport方法
此方法封装了记录日志和报告，使用是传入一个异常，方法会将异常记录到日志的warn级别，并调用exception方法进行报告。

其中一个使用场景是，数据库事务中发生非业务错误时，项目返回操作失败给前端，但是我们想知道发生的异常详情，这时可调用此方法，具体如下：


        DB::beginTransaction();
        try {

            //some code

            DB::commit();
        } catch (OPException $e) {
            //OPException是已知的业务异常，无需处理
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            //发生其他未知异常
            DB::rollBack();
            SelfReport::logAndReport($e);
            throw new OPException('操作失败', 30003, false);
        }

注意事项
一、如果你的代码中有使用异常的编写方式，并且异常不需要记录日志并发送至运维平台。如果异常未在代码中捕获，而抛至laravel的异常处理类，即app/Exceptions/Handler，请定义你的异常类型，并加入Handler类的$dontReport中，确保不会记录进laravel.log和发送至运维平台。

二、在项目开发阶段，或者本地开发的代码中，env中应该不含有APP_OPERATION_REPORT参数，确保开发阶段不会发送垃圾数据至运维平台。等项目上线后，由管理员在线上项目的env中加上APP_OPERATION_REPORT=true，确保加入运维报告中。

三、关于qq收不到邮件，请在发垃圾设置中添加邮件admin@operator.world白名单

