<?php

namespace ClickNow\Checker\Result;

use Doctrine\Common\Collections\ArrayCollection;

class ResultsCollection extends ArrayCollection
{
    /**
     * Is successfully?
     *
     * @return bool
     */
    public function isSuccessfully()
    {
        if ($this->count() > 0) {
            return !($this->filterByError()->count() > 0 || $this->filterByWarning()->count() > 0);
        }

        return false;
    }

    /**
     * Is failed?
     *
     * @return bool
     */
    public function isFailed()
    {
        if ($this->count() > 0) {
            return $this->filterByError()->count() > 0;
        }

        return false;
    }

    /**
     * Filter results by skipped.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    public function filterBySkipped()
    {
        return $this->filter(function (ResultInterface $result) {
            return $result->isSkipped();
        });
    }

    /**
     * Filter results by success.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    public function filterBySuccess()
    {
        return $this->filter(function (ResultInterface $result) {
            return $result->isSuccess();
        });
    }

    /**
     * Filter results by warning.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    public function filterByWarning()
    {
        return $this->filter(function (ResultInterface $result) {
            return $result->isWarning();
        });
    }

    /**
     * Filter results by error.
     *
     * @return \ClickNow\Checker\Result\ResultsCollection
     */
    public function filterByError()
    {
        return $this->filter(function (ResultInterface $result) {
            return $result->isError();
        });
    }

    /**
     * Get all messages from results.
     *
     * @return array
     */
    public function getAllMessages()
    {
        return array_values(array_filter($this->map(function (ResultInterface $result) {
            return $result->getMessage();
        })->toArray()));
    }
}
