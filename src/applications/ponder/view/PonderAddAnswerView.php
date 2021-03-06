<?php

final class PonderAddAnswerView extends AphrontView {

  private $question;
  private $actionURI;
  private $draft;

  public function setQuestion($question) {
    $this->question = $question;
    return $this;
  }

  public function setActionURI($uri) {
    $this->actionURI = $uri;
    return $this;
  }

  public function render() {
    $question = $this->question;
    $viewer = $this->user;

    $authors = mpull($question->getAnswers(), null, 'getAuthorPHID');
    if (isset($authors[$viewer->getPHID()])) {
      return id(new PHUIInfoView())
        ->setSeverity(PHUIInfoView::SEVERITY_NOTICE)
        ->setTitle(pht('Already Answered'))
        ->appendChild(
          pht(
            'You have already answered this question. You can not answer '.
            'twice, but you can edit your existing answer.'));
    }

    $info_panel = null;
    if ($question->getStatus() != PonderQuestionStatus::STATUS_OPEN) {
      $info_panel = id(new PHUIInfoView())
        ->setSeverity(PHUIInfoView::SEVERITY_WARNING)
        ->appendChild(
          pht(
            'This question has been marked as closed,
             but you can still leave a new answer.'));
    }

    $header = id(new PHUIHeaderView())
      ->setHeader(pht('Add Answer'));

    $form = new AphrontFormView();
    $form
      ->setUser($this->user)
      ->setAction($this->actionURI)
      ->setWorkflow(true)
      ->addHiddenInput('question_id', $question->getID())
      ->appendChild(
        id(new PhabricatorRemarkupControl())
          ->setName('answer')
          ->setLabel(pht('Answer'))
          ->setError(true)
          ->setID('answer-content')
          ->setUser($this->user))
      ->appendChild(
        id(new AphrontFormSubmitControl())
          ->setValue(pht('Add Answer')));

    $box = id(new PHUIObjectBoxView())
      ->setHeader($header)
      ->appendChild($form);

    if ($info_panel) {
      $box->setInfoView($info_panel);
    }

    return $box;
  }
}
