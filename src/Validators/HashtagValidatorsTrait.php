<?php

namespace WebDevBot\Validators;


trait HashtagValidatorsTrait
{
    public function areHashtagsOkFor($post)
    {
        if(preg_match('/(\[)?([ ]*)(#[a-zA-Z0-9 ]+)([ ]*)(\])?/', $post['message']) === 0)
            return false;

        return true;
    }
}