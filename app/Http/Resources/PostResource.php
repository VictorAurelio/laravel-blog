<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'title' => $this->title,
        'body' => $this->body,
        'picture' => $this->picture,
        'updated_at' => $this->updated_at,
        'author' => [
            'firstName' => $this->user->firstName,
            'lastName' => $this->user->lastName,
            'avatar' => $this->user->avatar,
        ],
      ];
    }
}
