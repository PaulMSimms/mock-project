// document.addEventListener('DOMContentLoaded', function () {
//     document.body.addEventListener('click', function (e) {
//         if (e.target.classList.contains('delete-blog')) {
//             const blogId = e.target.getAttribute('data-id');
//             if (confirm('Are you sure?')) {
//                 fetch(`/blogs/delete/${blogId}`, {
//                     method: 'DELETE'
//                 }).then(res => window.location.reload());
//             }
//         }
//     });
// });
