import { dateFormatter } from './date-formatter.js';

function getReplyForm(parentId = null) {
    const template = document.getElementById('reply-form-template');
    const form = template.content.cloneNode(true);

    if (parentId) {
        form.querySelector('.comment__form').dataset.id = parentId;
    } else {
        form.querySelector('.comment__form').classList.add('comment__form-main');
    }

    return form;
}

function getCommentAfterPublishing(data) {
    const template = document.getElementById('published-comment-template');
    const comment = template.content.cloneNode(true);
    const defaultAvatar = document.getElementById('default_avatar').value;

    if (!data.parent_id) {
        comment.children[0].classList.add('post__comment');
    } else {
        comment.children[0].classList.add('comment__reply');

    }

    comment.querySelector(".comment__action_edit").style.display = "inline";
    comment.querySelector(".comment__action_delete").style.display = "inline";
    comment.querySelector('.comment').setAttribute('data-id', data.id);
    comment.querySelector('.comment__author').textContent = data.author + ':';
    comment.querySelector('.comment__date').textContent = 'Только что';
    comment.querySelector('.comment__text').textContent = data.content;
    comment.querySelector('.avatar__image').src = '/images/' + (data.avatar ?? defaultAvatar);
    comment.querySelector('.comment__date').classList.add('ajax');

    return comment;
}

function getErrorsList(data) {
    const ul = document.createElement('ul');
    ul.classList.add('comment__errors');

    for (let key in data) {
        const li = document.createElement('li');
        li.classList.add('comment__error');
        li.textContent = data[key][0];
        ul.append(li);
    }

    return ul;
}

const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': token,
    'Accept': 'application/json'
};
const comments = document.querySelector('.post__comments');
const newReplyForm = getReplyForm();
const currentUserId = document.getElementById('auth_user').value;
const postId = document.getElementById('post_id').value;
const baseUrl = document.getElementById('base_url').value;
let url = baseUrl;

if (comments) {
    comments.insertBefore(newReplyForm, comments.firstElementChild.nextSibling);
}

comments.addEventListener('click', e => {
    const target = e.target;

    if (target.classList.contains('comment__action_reply')) {
        const comment = target.closest('.comment');
        const existingForm = comment.querySelector('form');

        if (existingForm) {
            existingForm.remove();
        } else {
            const allForms = comments.querySelectorAll('form');

            for (let i = 0; i < allForms.length; i++) {
                if (!allForms[i].classList.contains('comment__form-main'))
                    allForms[i].remove();
            }

            const parentId = comment.dataset.id
            comment.append(getReplyForm(parentId));
        }
    }
});

comments.addEventListener('submit', e => {
    const target = e.target;

    if (!target.classList.contains('comment__form') && !target.classList.contains('comment__form-edit'))
        return;

    e.preventDefault();

    const isNewComment = target.classList.contains('comment__form');
    const isEditComment = target.classList.contains('comment__form-edit');
    const method = isNewComment ? 'POST' : 'PUT';
    const parentElem = target.parentNode;
    const content = target.querySelector('.comment__textarea').value;
    const data = {
        content: content,
        post_id: postId,
        user_id: currentUserId,
        parent_id: +target.dataset.id || null
    };

    fetch(url, {
        method: method,
        headers: headers,
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                const nextElemSibling = target.nextElementSibling;

                if (!nextElemSibling || !nextElemSibling.classList.contains('comment__errors')) {
                    if (data.db_exception) {
                        alert(data.message);
                    } else {
                        target.after(getErrorsList(data.errors));
                    }
                }
            } else {
                if (isEditComment) {
                    target.parentNode.querySelector('.comment__text').textContent = data.content;
                    target.parentNode.querySelector('.comment').style.display = '';
                    target.remove();
                } else {
                    const commentElem = getCommentAfterPublishing(data);

                    if (!data.parent_id) {
                        target.after(commentElem);
                        target.children[0].value = '';
                    } else {
                        if (!parentElem.nextElementSibling) {
                            const wrap = document.createElement('div');
                            wrap.classList.add('comment__replies');
                            parentElem.parentNode.append(wrap);
                            wrap.append(commentElem);
                        } else {
                            parentElem.parentNode.querySelector('.comment__replies').prepend(commentElem);
                        }

                        target.parentNode.querySelector('.comment__action_reply').click();
                    }

                    dateFormatter(document.getElementsByClassName('ajax')[0], data.created_at, true);
                }
            }
        })
        .catch((error) => {
            console.error(error)
        })
        .finally(() => {
            url = baseUrl;
        })
});

comments.addEventListener('click', e => {
    const target = e.target;

    if (!target.classList.contains('comment__action_edit'))
        return;

    let parent_id;
    let t = target.closest('.comment__replies');
    let tp = t && t.previousElementSibling;

    if (tp) {
        if (tp.classList.contains('comment')) {
            parent_id = tp.dataset.id;
        } else {
            parent_id = tp.children[0].dataset.id;
        }
    } else {
        parent_id = null;
    }

    const comment = target.closest('.comment');
    const postOrReplyWrap = comment.parentNode;
    const originalText = comment.querySelector('.comment__text')?.textContent ?? '';
    const template = document.getElementById('reply-form-edit-template');
    const editForm = template.content.cloneNode(true);

    url += `/${comment.dataset.id}`;
    comment.style.display = 'none';
    editForm.querySelector('.comment__form-edit').setAttribute('data-id', parent_id);
    editForm.querySelector('.comment__textarea').value = originalText;
    postOrReplyWrap.prepend(editForm);
    postOrReplyWrap.querySelector('.comment__cancel-btn').addEventListener('click', e => {
        const target = e.target;
        const parent = target.parentNode;

        if (!target.classList.contains('comment__cancel-btn'))
            return;

        parent.remove();
        comment.style.display = '';
    });
});

comments.addEventListener('click', e => {
    const target = e.target;

    if (!target.classList.contains('comment__action_delete'))
        return;

    const comment = target.closest('.comment');
    const commentId = comment.dataset.id;
    const isReply = comment.parentNode.classList.contains('comment__reply');
    let hasReplies;
    let canDelete;

    if (isReply) {
        hasReplies = comment.nextElementSibling && comment.nextElementSibling.classList.contains('comment__replies');
    } else {
        hasReplies = comment.nextElementSibling;
    }

    canDelete = !hasReplies;
    url += `/${commentId}`;

    fetch(url, {
        method: 'DELETE',
        headers: headers,
        body: JSON.stringify({ canDelete: canDelete })
    })
        .then(response => response.json())
        .then(data => {
            if (data.deleted) {
                if (isReply) {
                    const replies = target.closest('.comment__replies');

                    if (replies.childNodes.length > 1) {
                        target.closest('.comment__reply').remove();
                    } else {
                        target.closest('.comment__replies').remove();
                    }
                } else {
                    target.closest('.post__comment').remove();
                }
            } else {
                comment.querySelector('.comment__text').textContent = data.message;
                comment.querySelector('.comment__action-group').style.display = 'none';
            }
        })
        .catch((error) => {
            console.error(error)
        })
        .finally(() => {
            url = baseUrl;
        });
});

Array.from(document.querySelectorAll('.comment__date')).forEach((elem) => {
    dateFormatter(elem, elem.textContent);
});
