<?php

class SiteController extends Controller
{

    public function actionIndex()
    {
        $this->checkUserIsGuest();

        $this->layout = "column1";

        $this->render('index', array(
            "hot" => Cacher::getHot(),
            "searchTop" => Cacher::getSearchTop(),
            "announces" => Cacher::getAnnounces(),
            "blog" => Cacher::getBlogPosts(),
        ));
    }

    private function checkUserIsGuest()
    {
        if (Yii::app()->user->isGuest) {
            $this->loginAttempt();
        }
    }

    private function loginAttempt()
    {
        if (Yii::app()->request->isPostRequest && isset($_POST["login"])) {
            $user = new User("login");
            $user->setAttributes($_POST["login"]);
            $user->remember = true;
            if ($user->login()) {
                $this->redirect("/");
            } else {
                Yii::app()->user->setFlash("error", $user->getError("pass"));
            }
        }
    }

    public function actionIni()
    {
        $area = $_POST["area"];
        unset($_POST["area"]);

        if (in_array($area, array("hot"))) {
            foreach ($_POST as $k => $v) {
                Yii::app()->user->ini->set($area . "." . $k, $v);
            }
            Yii::app()->user->ini->save();
        }

        $this->redirect("/");
    }

    public function actionHelp()
    {
        $this->layout = "column1";
        $this->render("help");
    }

    public function actionTOS()
    {
        $this->layout = "column1";
        $this->render("tos");
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo json_encode(array("error" => $error["message"]));
            else
                $this->render('error', $error);
        }
    }
}
